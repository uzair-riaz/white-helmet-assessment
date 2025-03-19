<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Services\Interfaces\CommentServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    protected $commentService;
    
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the comments for a task.
     */
    public function index(int $taskId): JsonResponse
    {
        try {
            $comments = $this->commentService->getTaskComments($taskId);
            
            return response()->json([
                'status' => 'success',
                'data' => $comments
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, int $taskId): JsonResponse
    {
        try {
            $comment = $this->commentService->createComment($request->validated(), $taskId);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Comment added successfully',
                'data' => $comment
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, int $taskId, int $id): JsonResponse
    {
        try {
            $comment = $this->commentService->updateComment($request->validated(), $taskId, $id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Comment updated successfully',
                'data' => $comment
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            $modelType = strpos($e->getMessage(), 'Task') !== false ? 'Task' : 'Comment';
            return response()->json([
                'status' => 'error',
                'message' => $modelType . ' not found'
            ], 404);
        }
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(int $taskId, int $id): JsonResponse
    {
        try {
            $this->commentService->deleteComment($taskId, $id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Comment deleted successfully'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            $modelType = strpos($e->getMessage(), 'Task') !== false ? 'Task' : 'Comment';
            return response()->json([
                'status' => 'error',
                'message' => $modelType . ' not found'
            ], 404);
        }
    }
}
