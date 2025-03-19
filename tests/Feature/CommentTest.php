<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /**
     * Test comment creation.
     */
    public function test_user_can_add_comment_to_task(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a task owned by the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Authentication
        $this->actingAs($user);
        
        // Comment data
        $commentData = [
            'content' => $this->faker->paragraph,
        ];
        
        // Send request
        $response = $this->postJson("/api/tasks/{$task->id}/comments", $commentData);
        
        // Assert response
        $response->assertStatus(201)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'content',
                         'task_id',
                         'user_id',
                         'created_at',
                         'updated_at',
                         'user' => [
                             'id',
                             'name'
                         ]
                     ]
                 ]);
        
        // Check database
        $this->assertDatabaseHas('comments', [
            'content' => $commentData['content'],
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
    }
    
    /**
     * Test listing comments.
     */
    public function test_user_can_list_comments(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a task owned by the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create comments for the task
        Comment::factory()->count(3)->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
        
        // Authentication
        $this->actingAs($user);
        
        // Send request
        $response = $this->getJson("/api/tasks/{$task->id}/comments");
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test updating comment.
     */
    public function test_user_can_update_comment(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a task owned by the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create a comment
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
        
        // Authentication
        $this->actingAs($user);
        
        // Update data
        $updateData = [
            'content' => 'Updated comment content',
        ];
        
        // Send request
        $response = $this->putJson("/api/tasks/{$task->id}/comments/{$comment->id}", $updateData);
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('data.content', 'Updated comment content');
        
        // Check database
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment content',
        ]);
    }
    
    /**
     * Test deleting comment.
     */
    public function test_user_can_delete_comment(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a task owned by the user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Create a comment
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
        
        // Authentication
        $this->actingAs($user);
        
        // Send request
        $response = $this->deleteJson("/api/tasks/{$task->id}/comments/{$comment->id}");
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success');
        
        // Check database
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }
    
    /**
     * Test user can only edit their own comments.
     */
    public function test_user_cannot_edit_others_comments(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Create a task
        $task = Task::factory()->create([
            'user_id' => $user1->id,
            'assigned_to' => $user2->id,
        ]);
        
        // Create a comment by user2
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user2->id,
        ]);
        
        // Authentication as user1
        $this->actingAs($user1);
        
        // Update data
        $updateData = [
            'content' => 'Trying to update another user\'s comment',
        ];
        
        // Send request
        $response = $this->putJson("/api/tasks/{$task->id}/comments/{$comment->id}", $updateData);
        
        // Assert user1 cannot edit user2's comment
        $response->assertStatus(403);
    }
}
