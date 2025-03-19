<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $taskRepository;
    protected $taskService;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $this->taskService = new TaskService($this->taskRepository);
    }
    
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_get_all_tasks_returns_user_tasks(): void
    {
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $tasks = Task::factory()->count(3)->make(['user_id' => $user->id]);
        
        $this->taskRepository->shouldReceive('getAllForUser')
            ->with($user->id)
            ->once()
            ->andReturn($tasks);
            
        $result = $this->taskService->getAllTasks();
        
        $this->assertEquals($tasks, $result);
    }
    
    public function test_get_task_by_id_returns_task_when_authorized(): void
    {
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $task = Task::factory()->make(['user_id' => $user->id]);
        
        $this->taskRepository->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($task);
            
        $result = $this->taskService->getTaskById(1);
        
        $this->assertEquals($task, $result);
    }
    
    public function test_get_task_by_id_throws_exception_when_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $task = Task::factory()->make([
            'user_id' => $otherUser->id,
            'assigned_to' => null
        ]);
        
        $this->taskRepository->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($task);
            
        $this->expectException(AuthorizationException::class);
        
        $this->taskService->getTaskById(1);
    }
    
    public function test_create_task_saves_task_with_current_user(): void
    {
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => '2023-12-31',
            'assigned_to' => null
        ];
        
        $expectedData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => '2023-12-31',
            'user_id' => $user->id,
            'assigned_to' => null
        ];
        
        $task = Task::factory()->make($expectedData);
        
        $this->taskRepository->shouldReceive('create')
            ->with($expectedData)
            ->once()
            ->andReturn($task);
            
        $result = $this->taskService->createTask($taskData);
        
        $this->assertEquals($task, $result);
    }
    
    public function test_update_task_throws_exception_when_unauthorized(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $task = Task::factory()->make([
            'user_id' => $otherUser->id,
            'assigned_to' => null
        ]);
        
        $this->taskRepository->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($task);
            
        $this->expectException(AuthorizationException::class);
        
        $this->taskService->updateTask(1, ['title' => 'Updated Title']);
    }
    
    public function test_delete_task_returns_true_when_successful(): void
    {
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);
        
        $task = Task::factory()->make(['user_id' => $user->id]);
        
        $this->taskRepository->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($task);
            
        $this->taskRepository->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);
            
        $result = $this->taskService->deleteTask(1);
        
        $this->assertTrue($result);
    }
} 