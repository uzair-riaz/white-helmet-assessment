<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_has_many_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);
        
        $this->assertCount(3, $user->tasks);
        $this->assertInstanceOf(Task::class, $user->tasks->first());
    }
    
    public function test_user_has_many_assigned_tasks(): void
    {
        $user = User::factory()->create();
        $assignedUser = User::factory()->create();
        
        Task::factory()->count(2)->create([
            'user_id' => $user->id,
            'assigned_to' => $assignedUser->id
        ]);
        
        $this->assertCount(2, $assignedUser->assignedTasks);
        $this->assertInstanceOf(Task::class, $assignedUser->assignedTasks->first());
    }
    
    public function test_user_has_many_comments(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        Comment::factory()->count(3)->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);
        
        $this->assertCount(3, $user->comments);
        $this->assertInstanceOf(Comment::class, $user->comments->first());
    }
    
    public function test_user_hidden_attributes(): void
    {
        $hidden = (new User())->getHidden();
        
        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }
    
    public function test_user_fillable_attributes(): void
    {
        $fillable = (new User())->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
    }
} 