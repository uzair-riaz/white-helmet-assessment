<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;

class AuthService implements AuthServiceInterface
{
    protected $userRepository;
    
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token
            ];
        });
    }
    
    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }
        
        // Use transaction to ensure token creation is successful
        return DB::transaction(function () use ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token
            ];
        });
    }
    
    public function logout(Request $request): bool
    {
        return DB::transaction(function () use ($request) {
            $request->user()->currentAccessToken()->delete();
            return true;
        });
    }
    
    public function getProfile(Request $request)
    {
        return $request->user();
    }
} 