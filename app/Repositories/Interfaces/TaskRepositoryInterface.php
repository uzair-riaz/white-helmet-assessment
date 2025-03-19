<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllForUser(int $userId): Collection;
    
    public function getById(int $id);
    
    public function create(array $data): Task;
    
    public function update(int $id, array $data): Task;
    
    public function delete(int $id): bool;
    
    public function getAllUsers();
} 