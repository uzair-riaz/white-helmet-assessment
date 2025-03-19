<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    protected $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return $this->user->create($data);
        });
    }
    
    public function findByEmail(string $email)
    {
        return $this->user->where('email', $email)->first();
    }
    
    public function getCurrentUser()
    {
        return Auth::user();
    }
} 