<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportDisposition;
use App\Models\ReportStatusLog;
use App\Models\Report;
use App\Models\ReportImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start building the query for the mobile app's home feed.
        $query = Report::with([
            'user:id,name', // The user who created the report
            'images', // Report images with full URL path
            'latestStatusLog.user:id,name', // The user who last updated the status
            'latestStatusLog.position:id,name', // The position of the user who last updated
        ]);

        // Apply s filter if the 'search' parameter is provided.
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter if the 'status' parameter is provided.
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Order by the latest activity (from status logs) and paginate the results.
        $reports = $query->orderByDesc(
            ReportStatusLog::select('created_at')
                ->whereColumn('report_id', 'reports.id')
                ->latest()
                ->take(1)
        )
            ->where('status', '!=', Report::CANCELLED) // Exclude cancelled reports
            ->where('status', '!=', Report::SUBMITTED) // Exclude submitted reports
            ->paginate(15);

        return response()->json($reports);
    }

    /**
     * Display a listing of the reports created by the authenticated user.
     */
    public function myReports(Request $request)
    {
        $user = auth()->user();

        // Start building the query, filtering by the authenticated user's ID.
        $query = Report::where('user_id', $user->id)
            ->with([
                'user:id,name', // The user who created the report (which is the current user)
                'images', // Report images with full URL path
                'latestStatusLog.user:id,name', // The user who last updated the status
                'latestStatusLog.position:id,name', // The position of the user who last updated
            ]);

        // Apply search filter if the 'search' parameter is provided.
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply status filter if the 'status' parameter is provided.
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Order by the latest activity (from status logs) and paginate the results.
        $reports = $query->orderByDesc(
            ReportStatusLog::select('created_at')
                ->whereColumn('report_id', 'reports.id')
                ->latest()
                ->take(1)
        )->paginate(15);

        return response()->json($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'temp_position_id' => 'nullable|exists:positions,id', // Validate if the position exists
            // 'images' => 'nullable|array',
            // 'images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Max 2MB per image
            'images' => 'nullable|array|min:1',
            'images.*.image_id' => 'required_with:images',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $indexData = 0;
            $images = [];
            $errors = [];

            if ($request->filled('images')) {
                foreach ($request->images as $key => $image) {
                    $indexData += 1;
                    $errorCount = 0;
                    $img = ReportImage::where('id', $image['image_id'])->first();

                    if ($img == null) {
                        $errorCount++;
                        $errors['image_id.' . $key] = [trans('validation.in', ['attribute' => trans('validation.attributes.image_id') . ' ' . $indexData])];
                    } else {
                        $images[$img->id] = $img;
                    }
                }
            }

            if (count($errors) != 0) {
                DB::rollBack();
                $error = ['errors' => $errors];
                return response()->json($error, 422);
            }


            // Create the report.
            // The 'created' event in the Report model will automatically create the initial status log.
            $report = Report::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->content,
                'status' => Report::SUBMITTED,
                'temp_position_id' => $request->temp_position_id,
            ]);

            if ($request->filled('images')) {
                foreach ($request->images as $key => $image) {
                    $img = $images[$image['image_id']];
                    $reportImages = ReportImage::findOrFail($img->id);
                    $reportImages->report_id = $report->id;
                    $reportImages->is_temporary = false;
                    $reportImages->save();
                }
            }

            DB::commit();

            // Load relations for the response.
            $report->load(['user:id,name', 'images']);

            return response()->json($report, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create report', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        $user = auth()->user();
        $canTakeAction = false;

        // Determine if the current authenticated user can perform an action on this report.
        // This logic is for users with a position, mirroring the confirmation logic in the web admin panel.
        if ($user && $user->position_id) {
            // 1. The report must not be in a final state (e.g., completed or rejected).
            $isFinalState = in_array($report->status, [
                Report::SUCCESS,
                Report::REJECTED,
                Report::CANCELLED,
            ]);

            if (!$isFinalState) {
                // 2. The report must be assigned to the current user's position.
                // We check this by looking at the 'to_position_id' of the latest disposition.
                $latestDisposition = $report->dispositions()->latest()->first();

                if ($latestDisposition && $latestDisposition->to_position_id == $user->position_id) {
                    // 3. The user (identified by their position) must not have already acted on this report.
                    // This prevents the confirmation menu from appearing again after an action has been taken.
                    // This logic is consistent with the $isAlreadyMakeConfirmation check in ReportCrudController.
                    $hasAlreadyActed = $report->statusLogs()
                        ->where('position_id', $user->position_id)
                        ->exists();

                    if (!$hasAlreadyActed) {
                        $canTakeAction = true;
                    }
                }
            }
        }

        // Load the report with all its necessary relations for the detail view.
        $report->load([
            'user:id,name', // The user who created the report
            'images', // Report images with full URL path
            'statusLogs' => function ($query) {
                $query->with([
                    'user:id,name', // User who created the log
                    'position:id,name', // Position of the user who created the log
                    'disposition' => function ($query) {
                        // Load disposition details including the positions involved
                        $query->with(['fromPosition:id,name', 'toPosition:id,name']);
                    }
                ])->orderBy('created_at', 'asc');
            }
        ]);

        // Add the 'can_take_action' flag to the response object.
        // The mobile app can use this to decide whether to show the confirmation/disposition menu.
        $report->can_take_action = $canTakeAction;

        // Return the report data as a JSON response.
        return response()->json($report);
    }

    /**
     * Allows a user to cancel their own report if it's still in 'submitted' status.
     */
    public function cancelReport(Report $report)
    {
        $user = auth()->user();

        // Authorization Check 1: Ensure the authenticated user is the owner of the report.
        if ($report->user_id != $user->id) {
            return response()->json(['message' => 'You are not authorized to cancel this report.'], 403);
        }

        // Authorization Check 2: Ensure the report can be cancelled (only when 'submitted').
        if ($report->status != Report::SUBMITTED) {
            return response()->json(['message' => 'This report cannot be cancelled as it is already being processed.'], 422);
        }

        // Proceed with cancellation within a database transaction.
        DB::beginTransaction();
        try {
            // Create a status log entry for the cancellation action.
            $report->createStatusLog(
                $user->id,
                Report::CANCELLED,
                'Laporan dibatalkan oleh pengguna.'
            );

            // Update the report's status and save it.
            $report->status = Report::CANCELLED;
            $report->save();

            DB::commit();

            return response()->json(['message' => 'Report successfully cancelled.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to cancel report.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process an action (complete, reject, disposition) on a report by a user with a position.
     */
    public function processReportAction(Request $request, Report $report)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'string', Rule::in(['completed', 'rejected', 'disposition'])],
            'note' => 'required|string|max:1000',
            'position_id' => 'required_if:action,disposition|exists:positions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        // 2. Authorization
        // a. User must have a position to perform this action.
        if (!$user->position_id) {
            return response()->json(['message' => 'You do not have a position assigned and cannot perform this action.'], 403);
        }

        // b. Report must not be in a final state.
        if (in_array($report->status, [Report::SUCCESS, Report::REJECTED, Report::CANCELLED])) {
            return response()->json(['message' => 'This report is already in a final state and cannot be processed further.'], 422);
        }

        // c. Report must be assigned to the user's position.
        $latestDisposition = $report->dispositions()->latest()->first();
        if (!$latestDisposition || $latestDisposition->to_position_id != $user->position_id) {
            return response()->json(['message' => 'This report is not assigned to your position.'], 403);
        }

        // d. User (by position) must not have already acted on this report.
        if ($report->statusLogs()->where('position_id', $user->position_id)->exists()) {
            return response()->json(['message' => 'An action has already been taken from your position for this report.'], 422);
        }

        // 3. Business Logic (mirroring ReportCrudController)
        DB::beginTransaction();
        try {
            $action = $request->input('action');
            $note = $request->input('note');

            switch ($action) {
                case 'completed':
                    $report->createStatusLog($user->id, Report::SUCCESS, $note, $user->position_id);
                    $report->status = Report::SUCCESS;
                    break;

                case 'rejected':
                    $report->createStatusLog($user->id, Report::REJECTED, $note, $user->position_id);
                    $report->status = Report::REJECTED;
                    break;

                case 'disposition':
                    $disposition = ReportDisposition::create([
                        'report_id' => $report->id,
                        'from_position_id' => $user->position_id,
                        'to_position_id' => $request->input('position_id'),
                        'note' => $note,
                    ]);

                    $report->createStatusLog($user->id, Report::PENDING, $note, $user->position_id, $disposition->id);
                    $report->status = Report::PENDING;
                    break;
            }

            $report->save();
            DB::commit();

            return response()->json(['message' => 'Report action processed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process report action.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $user = auth()->user();

        // 1. Authorization: Ensure the user owns the report and it's in a modifiable state.
        if ($report->user_id != $user->id) {
            return response()->json(['message' => 'You are not authorized to edit this report.'], 403);
        }

        if ($report->status != Report::SUBMITTED) {
            return response()->json(['message' => 'This report cannot be edited as it is already being processed.'], 422);
        }

        // 2. Validation
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'images' => 'nullable|array|min:1',
            'images.*.image_id' => 'required_with:images',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $errors = [];
            $indexData = 0;
    
            $incomingImages = collect($request->images ?? []);
            $incomingImageIds = $incomingImages->pluck('image_id')->filter()->toArray();
    
            // Ambil gambar lama
            $imagesBefore = ReportImage::where('report_id', $id)->get()->keyBy('id');
    
            // Cari gambar lama yang tidak ada di list baru (harus dihapus)
            $imageDeleteIds = $imagesBefore->keys()->diff($incomingImageIds)->toArray();
    
            // Hapus file gambar dari storage & DB
            foreach ($imageDeleteIds as $imgId) {
                $img = $imagesBefore->get($imgId);
                if ($img && $img->image_path && Storage::exists($img->getRawOriginal('image_path'))) {
                    Storage::delete($img->getRawOriginal('image_path'));
                }
            }
            ReportImage::whereIn('id', $imageDeleteIds)->delete();
    
            // Validasi gambar baru/lama dari request
            $validImages = [];
            foreach ($incomingImages as $key => $image) {
                $indexData++;
                $img = ReportImage::find($image['image_id']);
    
                if (!$img) {
                    $errors["image_id.$key"] = [trans('validation.in', [
                        'attribute' => trans('validation.attributes.image_id') . " $indexData"
                    ])];
                } else {
                    $validImages[$img->id] = $img;
                }
            }
    
            if (!empty($errors)) {
                DB::rollBack();
                return response()->json(['errors' => $errors], 422);
            }

            // Update text fields if they are present in the request.
            $report->update($request->only(['title', 'content']));
    
            // Update relasi report_id dan flag temporary
            foreach ($validImages as $img) {
                $img->report_id = $report->id;
                $img->is_temporary = false;
                $img->save();
            }

            DB::commit();

            // Return the updated report with its relations.
            $report->load(['user:id,name', 'images']);
            return response()->json($report);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update report.', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image_path' => ['required', 'image', 'max:10000'],
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan dulu record kosong untuk dapatkan id
            $reportImage = new ReportImage();
            $reportImage->created_by = auth()->user()->id;
            $reportImage->is_temporary = true;
            $reportImage->save();

            // 2. Generate nama file: {id}_{timestamp}_{random}.{ext}
            $file = $request->file('image_path');
            $id = $reportImage->id;
            $timestamp = now()->timestamp;
            $random = mt_rand(100000, 999999);
            $ext = $file->getClientOriginalExtension();
            $filename = "{$id}_{$timestamp}_{$random}.{$ext}";

            // 3. Simpan file ke storage
            $path = $file->storeAs('images/reports', $filename, 'public');

            // 4. Update image_path di DB
            $reportImage->image_path = $path;
            $reportImage->save();

            DB::commit();

            return response()->json($reportImage, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to upload image', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
