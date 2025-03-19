<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    public function getAllForTask(int $taskId): Collection;
    
    public function create(array $data): Comment;
    
    public function update(int $id, array $data): Comment;
    
    public function delete(int $id): bool;
    
    public function getById(int $id);
} 