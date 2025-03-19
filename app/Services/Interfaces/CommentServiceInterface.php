<?php

namespace App\Services\Interfaces;

interface CommentServiceInterface
{
    public function getTaskComments(int $taskId);
    
    public function createComment(array $data, int $taskId);
    
    public function updateComment(array $data, int $taskId, int $id);
    
    public function deleteComment(int $taskId, int $id): bool;
} 