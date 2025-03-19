<?php

namespace App\Providers;

use App\Repositories\CommentRepository;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CommentService;
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\Interfaces\CommentServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        
        // Services
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(CommentServiceInterface::class, CommentService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 