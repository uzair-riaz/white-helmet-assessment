<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected $taskService;
    
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the tasks.
     */
    public function index(): JsonResponse
    {
        $tasks = $this->taskService->getAllTasks();
        
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
        $task = $this->taskService->createTask($request->validated());
        
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
        try {
            $task = $this->taskService->getTaskById($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $task
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($id, $request->validated());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $task
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->taskService->deleteTask($id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Get list of users for assignment
     */
    public function getUsers(): JsonResponse
    {
        $users = $this->taskService->getUsers();
        
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
}
