<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_comment_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);
        
        $this->assertEquals($user->id, $comment->user->id);
        $this->assertInstanceOf(User::class, $comment->user);
    }
    
    public function test_comment_belongs_to_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);
        
        $this->assertEquals($task->id, $comment->task->id);
        $this->assertInstanceOf(Task::class, $comment->task);
    }
    
    public function test_comment_fillable_attributes(): void
    {
        $fillable = (new Comment())->getFillable();
        
        $this->assertContains('task_id', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('content', $fillable);
    }
} 