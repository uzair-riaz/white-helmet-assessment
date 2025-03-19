<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class TaskService implements TaskServiceInterface
{
    protected $taskRepository;
    
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    
    public function getAllTasks()
    {
        return $this->taskRepository->getAllForUser(Auth::id());
    }
    
    public function getTaskById(int $id)
    {
        $task = $this->taskRepository->getById($id);
        
        // Check if user has access to this task
        if ($task->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
            throw new AuthorizationException('Unauthorized access to task');
        }
        
        return $task;
    }
    
    public function createTask(array $data)
    {
        return DB::transaction(function () use ($data) {
            $taskData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
                'due_date' => $data['due_date'],
                'user_id' => Auth::id(),
                'assigned_to' => $data['assigned_to'] ?? null
            ];
            
            return $this->taskRepository->create($taskData);
        });
    }
    
    public function updateTask(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $task = $this->taskRepository->getById($id);
            
            // Check if user has permission to update
            if ($task->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
                throw new AuthorizationException('Unauthorized to update this task');
            }
            
            return $this->taskRepository->update($id, $data);
        });
    }
    
    public function deleteTask(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $task = $this->taskRepository->getById($id);
            
            // Check if user has permission to delete
            if ($task->user_id !== Auth::id()) {
                throw new AuthorizationException('Unauthorized to delete this task');
            }
            
            return $this->taskRepository->delete($id);
        });
    }
    
    public function getUsers()
    {
        return $this->taskRepository->getAllUsers();
    }
} 