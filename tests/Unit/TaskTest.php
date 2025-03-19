<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_task_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $this->assertEquals($user->id, $task->user->id);
        $this->assertInstanceOf(User::class, $task->user);
    }
    
    public function test_task_belongs_to_assigned_user(): void
    {
        $user = User::factory()->create();
        $assignedUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'assigned_to' => $assignedUser->id
        ]);
        
        $this->assertEquals($assignedUser->id, $task->assignedUser->id);
        $this->assertInstanceOf(User::class, $task->assignedUser);
    }
    
    public function test_task_has_many_comments(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        Comment::factory()->count(3)->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);
        
        $this->assertCount(3, $task->comments);
        $this->assertInstanceOf(Comment::class, $task->comments->first());
    }
    
    public function test_task_casts_due_date_as_date(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => '2023-12-31'
        ]);
        
        $this->assertIsObject($task->due_date);
        $this->assertEquals('2023-12-31', $task->due_date->toDateString());
    }
    
    public function test_task_fillable_attributes(): void
    {
        $fillable = (new Task())->getFillable();
        
        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('due_date', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('assigned_to', $fillable);
    }
} 