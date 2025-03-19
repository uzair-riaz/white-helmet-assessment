<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskRepository implements TaskRepositoryInterface
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getAll(): Collection
    {
        return Cache::remember('tasks.all', 60, function () {
            return $this->task->with(['user:id,name', 'assignedUser:id,name'])
                ->latest()
                ->get();
        });
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
        try {
            return Cache::remember('tasks.' . $id, 60, function () use ($id) {
                return $this->task->with(['user:id,name', 'assignedUser:id,name', 'comments.user:id,name'])
                    ->findOrFail($id);
            });
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Task with ID {$id} not found", $e->getCode(), $e);
        }
    }

    public function create(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->task->create($data);

            Cache::forget('tasks.all');
            Cache::forget('tasks.all.' . $data['user_id']);
            if (isset($data['assigned_to']) && $data['assigned_to']) {
                Cache::forget('tasks.all.' . $data['assigned_to']);
            }

            return $task;
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            try {
                $task = $this->task->findOrFail($id);
                $task->update($data);

                Cache::forget('tasks.all');
                Cache::forget('tasks.' . $id);
                Cache::forget('tasks.all.' . $task->user_id);
                if ($task->assigned_to) {
                    Cache::forget('tasks.all.' . $task->assigned_to);
                }

                return $task;
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException("Task with ID {$id} not found", $e->getCode(), $e);
            }
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            try {
                $task = $this->task->findOrFail($id);

                $assignedTo = $task->assigned_to;
                $userId = $task->user_id;

                $result = $task->delete();

                Cache::forget('tasks.all');
                Cache::forget('tasks.' . $id);
                Cache::forget('tasks.all.' . $userId);
                if ($assignedTo) {
                    Cache::forget('tasks.all.' . $assignedTo);
                }

                return $result;
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException("Task with ID {$id} not found", $e->getCode(), $e);
            }
        });
    }

    public function getAllUsers()
    {
        return User::select('id', 'name', 'email')->get();
    }
}
