<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Jobs\SendNewCommentNotification;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a task.
     */
    public function index(int $taskId): JsonResponse
    {
        // Check if the user has access to the task
        $task = Task::findOrFail($taskId);
        if ($task->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Use cache to improve performance
        $comments = Cache::remember('task.' . $taskId . '.comments', 60, function () use ($taskId) {
            return Comment::with('user:id,name')
                ->where('task_id', $taskId)
                ->latest()
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'data' => $comments
        ]);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, int $taskId): JsonResponse
    {
        $comment = Comment::create([
            'content' => $request->content,
            'task_id' => $taskId,
            'user_id' => Auth::id()
        ]);

        // Clear cache
        Cache::forget('task.' . $taskId . '.comments');
        Cache::forget('tasks.' . $taskId);

        // Dispatch job to send notification to task owner
        SendNewCommentNotification::dispatch($comment);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'data' => $comment->load('user:id,name')
        ], 201);
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, int $taskId, int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->update([
            'content' => $request->content
        ]);

        // Clear cache
        Cache::forget('task.' . $taskId . '.comments');
        Cache::forget('tasks.' . $taskId);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully',
            'data' => $comment->load('user:id,name')
        ]);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(int $taskId, int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        // Check if the user is the author of the comment or the task owner
        $task = Task::findOrFail($taskId);
        if ($comment->user_id !== Auth::id() && $task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $comment->delete();

        // Clear cache
        Cache::forget('task.' . $taskId . '.comments');
        Cache::forget('tasks.' . $taskId);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully'
        ]);
    }
}
