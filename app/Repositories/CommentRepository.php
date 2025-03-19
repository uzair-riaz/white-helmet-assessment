<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CommentRepository implements CommentRepositoryInterface
{
    protected $comment;
    
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
    
    public function getAllForTask(int $taskId): Collection
    {
        return Cache::remember('task.' . $taskId . '.comments', 60, function () use ($taskId) {
            return $this->comment->with('user:id,name')
                ->where('task_id', $taskId)
                ->latest()
                ->get();
        });
    }
    
    public function create(array $data): Comment
    {
        $comment = $this->comment->create($data);
        
        // Clear cache
        Cache::forget('task.' . $data['task_id'] . '.comments');
        Cache::forget('tasks.' . $data['task_id']);
        
        return $comment->load('user:id,name');
    }
    
    public function update(int $id, array $data): Comment
    {
        $comment = $this->comment->findOrFail($id);
        $comment->update($data);
        
        // Clear cache
        Cache::forget('task.' . $comment->task_id . '.comments');
        Cache::forget('tasks.' . $comment->task_id);
        
        return $comment->load('user:id,name');
    }
    
    public function delete(int $id): bool
    {
        $comment = $this->comment->findOrFail($id);
        $taskId = $comment->task_id;
        
        $result = $comment->delete();
        
        // Clear cache
        Cache::forget('task.' . $taskId . '.comments');
        Cache::forget('tasks.' . $taskId);
        
        return $result;
    }
    
    public function getById(int $id)
    {
        return $this->comment->findOrFail($id);
    }
} 