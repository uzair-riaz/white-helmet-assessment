<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(): JsonResponse
    {
        // Use cache to improve performance (optional)
        $tasks = Cache::remember('tasks.all.' . Auth::id(), 60, function () {
            return Task::with(['user:id,name', 'assignedUser:id,name'])
                ->where(function ($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhere('assigned_to', Auth::id());
                })
                ->latest()
                ->get();
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
            'assigned_to' => $request->assigned_to
        ]);

        // Clear cache
        Cache::forget('tasks.all.' . Auth::id());
        if ($request->assigned_to) {
            Cache::forget('tasks.all.' . $request->assigned_to);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        $task = Cache::remember('tasks.' . $id, 60, function () use ($id) {
            return Task::with(['user:id,name', 'assignedUser:id,name', 'comments.user:id,name'])
                ->findOrFail($id);
        });

        // Check if user has access to this task
        if ($task->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $task
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $task->update($request->all());

        // Clear cache
        Cache::forget('tasks.' . $id);
        Cache::forget('tasks.all.' . Auth::id());
        if ($task->assigned_to) {
            Cache::forget('tasks.all.' . $task->assigned_to);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        // Check if user has permission to delete
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        // Store assigned_to for cache clearing
        $assignedTo = $task->assigned_to;

        $task->delete();

        // Clear cache
        Cache::forget('tasks.' . $id);
        Cache::forget('tasks.all.' . Auth::id());
        if ($assignedTo) {
            Cache::forget('tasks.all.' . $assignedTo);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Get list of users for assignment
     */
    public function getUsers(): JsonResponse
    {
        $users = User::select('id', 'name', 'email')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
}
