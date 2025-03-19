<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    protected $task;
    
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
    
    public function getAllForUser(int $userId): Collection
    {
        return Cache::remember('tasks.all.' . $userId, 60, function () use ($userId) {
            return $this->task->with(['user:id,name', 'assignedUser:id,name'])
                ->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->orWhere('assigned_to', $userId);
                })
                ->latest()
                ->get();
        });
    }
    
    public function getById(int $id)
    {
        return Cache::remember('tasks.' . $id, 60, function () use ($id) {
            return $this->task->with(['user:id,name', 'assignedUser:id,name', 'comments.user:id,name'])
                ->findOrFail($id);
        });
    }
    
    public function create(array $data): Task
    {
        $task = $this->task->create($data);
        
        // Clear cache
        Cache::forget('tasks.all.' . $data['user_id']);
        if (isset($data['assigned_to']) && $data['assigned_to']) {
            Cache::forget('tasks.all.' . $data['assigned_to']);
        }
        
        return $task;
    }
    
    public function update(int $id, array $data): Task
    {
        $task = $this->task->findOrFail($id);
        $task->update($data);
        
        // Clear cache
        Cache::forget('tasks.' . $id);
        Cache::forget('tasks.all.' . $task->user_id);
        if ($task->assigned_to) {
            Cache::forget('tasks.all.' . $task->assigned_to);
        }
        
        return $task;
    }
    
    public function delete(int $id): bool
    {
        $task = $this->task->findOrFail($id);
        
        // Store assigned_to for cache clearing
        $assignedTo = $task->assigned_to;
        $userId = $task->user_id;
        
        $result = $task->delete();
        
        // Clear cache
        Cache::forget('tasks.' . $id);
        Cache::forget('tasks.all.' . $userId);
        if ($assignedTo) {
            Cache::forget('tasks.all.' . $assignedTo);
        }
        
        return $result;
    }
    
    public function getAllUsers()
    {
        return User::select('id', 'name', 'email')->get();
    }
} 