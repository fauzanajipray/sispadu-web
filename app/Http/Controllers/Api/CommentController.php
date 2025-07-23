<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // List komentar (dengan pagination)
    public function index(Request $request, $reportId)
    {
        $comments = Comment::with('user')
            ->where('report_id', $reportId)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 3));
        return response()->json($comments);
    }

    // Tambah komentar
    public function store(Request $request, $reportId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $comment = Comment::create([
            'report_id' => $reportId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);
        return response()->json($comment->load('user'), 201);
    }

    // Edit komentar
    public function update(Request $request, $reportId, $id)
    {
        $comment = Comment::where('report_id', $reportId)->findOrFail($id);
        if ($comment->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $comment->update(['content' => $request->content]);
        return response()->json($comment->load('user'));
    }

    // Hapus komentar
    public function destroy($reportId, $id)
    {
        $comment = Comment::where('report_id', $reportId)->findOrFail($id);
        if ($comment->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $comment->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
