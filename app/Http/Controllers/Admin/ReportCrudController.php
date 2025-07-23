<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportRequest;
use App\Models\Report;
use App\Models\ReportDisposition;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        $role = backpack_auth()->user()->role;
        if ($role == 'superadmin') {
            // Tampilkan semua data
        } else {
            Report::addGlobalScope('user_id', function ($builder) {
                $builder->whereHas('dispositions', function ($query) {
                    $query->where('to_position_id', backpack_auth()->user()->position_id)
                        ->orWhere('from_position_id', backpack_auth()->user()->position_id);
                });
            });
        }
        CRUD::setModel(Report::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report');
        CRUD::setEntityNameStrings('laporan', 'laporan');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->setupListColumn();
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    private function setupListColumn()
    {
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => __('base.reported_by'),
            'type' => 'relationship',
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name',
        ]);
        CRUD::column('user_id');
        CRUD::column('content');
        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'allows_null' => false,
            'value' => function ($entry) {
                return strtoupper($entry->status);
            },
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status) {
                        case Report::SUBMITTED:
                            return 'badge bg-primary';
                        case Report::PENDING:
                            return 'badge bg-warning';
                        case Report::SUCCESS:
                            return 'badge bg-success';
                        case Report::REJECTED:
                            return 'badge badge-danger';
                        case Report::CANCELLED:
                            return 'badge badge-secondary';
                        default:
                            return 'badge bg-light';
                    }
                },
            ],
        ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }



    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReportRequest::class);

        CRUD::field('user_id');
        CRUD::field('content');
        CRUD::field('status');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => __('base.reported_by'),
            'type' => 'relationship',
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name',
        ]);
        CRUD::column('user_id');
        CRUD::column('content')->type('textarea');
        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'allows_null' => false,
            'value' => function ($entry) {
                return strtoupper($entry->status);
            },
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status) {
                        case Report::SUBMITTED:
                            return 'badge bg-primary';
                        case Report::PENDING:
                            return 'badge bg-warning';
                        case Report::SUCCESS:
                            return 'badge bg-success';
                        case Report::REJECTED:
                            return 'badge badge-danger';
                        case Report::CANCELLED:
                            return 'badge badge-secondary';
                        default:
                            return 'badge bg-light';
                    }
                },
            ],
        ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::addColumn([
            'name' => 'images', // Relation name
            'label' => 'Images',
            'type' => 'custom_html',
            'value' => function ($entry) {
                $imagesHtml = '';
                foreach ($entry->images as $image) {
                    $imagesHtml .= '<a href="' . $image->image_path . '" target="_blank">
                                        <img src="' . $image->image_path . '" style="max-width: 100px; height: auto; margin-right: 5px;">
                                    </a>';
                }
                return $imagesHtml;
            },
        ]);
    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');


        $entry = $this->crud->getEntry($id);
        $isAlreadyMakeConfirmation = true;
        if (backpack_auth()->user()->role != 'superadmin') {
            $cekData = $entry->statusLogs()->where('position_id', backpack_auth()->user()->position_id)->first();
            if (!$cekData) {
                $isAlreadyMakeConfirmation = false;
            } else {
                $isAlreadyMakeConfirmation = true;
            }
        } else{
            if ($entry->status == Report::SUBMITTED) {
                $isAlreadyMakeConfirmation = false;
            } 
        }
        // dd($isAlreadyMakeConfirmation);
        $this->data['entry'] = $entry;
        $this->data['crud'] = $this->crud;
        $this->data['isDone'] = $entry->status == Report::SUCCESS || $entry->status == Report::REJECTED || $entry->status == Report::CANCELLED || $isAlreadyMakeConfirmation;
        $this->data['reportHistories'] = $entry->statusLogs()->with(['user', 'position', 'disposition' => function ($query) {
            $query->with(['fromPosition', 'toPosition']);
        }])->orderBy('created_at', 'asc')->get();
        return view('reports.show', $this->data);
    }


    public function listPositionReports(Request $request, $id)
    {
        // $this->crud->hasAccessOrFail('listPositionReports');

        $perPage = $request->get('perPage', 10);
        $perPage = $perPage == -1 ? 9999 : $perPage;

        $reports = Report::whereHas('dispositions', function ($query) use ($id) {
            $query->where('to_position_id', $id);
        })->with('user')->paginate($perPage);

        return response()->json([
            'rows' => view('reports.partials._report_rows', compact('reports'))->render(),
            'pagination' => view('reports.partials._report_pagination', compact('reports'))->render()
        ]);
    }

    public function confirmReport(Request $request)
    {
        DB::beginTransaction();
        try {
            $report = Report::with(['dispositions', 'statusLogs'])->findOrFail($request->report_id);

            switch ($request->action) {
                case 'completed':
                    // dd(backpack_auth()->user()->position_id);
                    $report->createStatusLog(
                        backpack_auth()->user()->id,
                        Report::SUCCESS,
                        $request->note,
                        backpack_auth()->user()->position_id
                    );
                    $report->status = Report::SUCCESS;
                    break;
                case 'rejected':
                    $report->createStatusLog(
                        backpack_auth()->user()->id,
                        Report::REJECTED,
                        $request->note,
                        backpack_auth()->user()->position_id
                    );
                    $report->status = Report::REJECTED;
                    break;
                case 'disposition':
                    $disposition = ReportDisposition::create([
                        'report_id' => $report->id,
                        'from_position_id' => backpack_auth()->user()->position_id,
                        'to_position_id' => $request->position_id,
                        'note' => $request->note, // Menyimpan catatan disposisi
                    ]);
                    $report->createStatusLog(
                        backpack_auth()->user()->id,
                        Report::PENDING,
                        $request->note,
                        backpack_auth()->user()->position_id,
                        $disposition->id // Menyimpan ID disposisi jika ada
                    );
                    $report->status = Report::PENDING;
                    // $report->dispositions()->create([
                    //     'to_position_id' => $request->position_id,
                    //     'note' => $request->note, // Menyimpan catatan
                    // ]);
                    break;
            }
            $report->save();
            $report = Report::with(['dispositions', 'statusLogs'])->findOrFail($request->report_id);
            // dd($report->toArray());
            // throw new \Exception('Debugging: ' . $report->status); // Uncomment this line to debug
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            // dd($e);
            // throw $e; // Uncomment this line to debug
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui laporan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
