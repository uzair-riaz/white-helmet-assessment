<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_all_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'due_date',
                        'user_id',
                        'assigned_to',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();
        
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Task created successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'due_date'
                ]
            ]);
            
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        $updatedData = [
            'title' => 'Updated Task Title',
            'status' => 'pending',
        ];

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Task updated successfully',
            ]);
            
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Task deleted successfully',
            ]);
            
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_view_other_users_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
} 