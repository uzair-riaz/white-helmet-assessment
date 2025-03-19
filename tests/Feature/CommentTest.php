<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_task_comments(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        Comment::factory()->count(3)->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}/comments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'task_id',
                        'user_id',
                        'content',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name'
                        ]
                    ]
                ]
            ]);
    }

    public function test_user_can_create_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $commentData = [
            'content' => 'This is a test comment',
        ];

        $response = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/comments", $commentData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Comment added successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'task_id',
                    'user_id',
                    'content',
                    'created_at',
                    'updated_at'
                ]
            ]);
            
        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment',
        ]);
    }

    public function test_user_can_update_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);
        
        $updatedData = [
            'content' => 'Updated comment text',
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}/comments/{$comment->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Comment updated successfully',
            ]);
            
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment text',
        ]);
    }

    public function test_user_can_delete_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Comment deleted successfully',
            ]);
            
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_modify_others_comment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user1->id
        ]);
        
        $updatedData = [
            'content' => 'Trying to update someone else\'s comment',
        ];

        $response = $this->actingAs($user2)
            ->putJson("/api/tasks/{$task->id}/comments/{$comment->id}", $updatedData);

        $response->assertStatus(403);
    }
} 