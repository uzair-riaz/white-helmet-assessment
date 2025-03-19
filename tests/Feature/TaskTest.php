<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test task creation.
     */
    public function test_user_can_create_task(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Authentication
        $this->actingAs($user);
        
        // Task data
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => 'pending',
            'due_date' => $this->faker->date(),
        ];
        
        // Send request
        $response = $this->postJson('/api/tasks', $taskData);
        
        // Assert response
        $response->assertStatus(201)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'description',
                         'status',
                         'due_date',
                         'user_id',
                         'assigned_to',
                         'created_at',
                         'updated_at',
                     ]
                 ]);
        
        // Check database
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'user_id' => $user->id,
        ]);
    }
    
    /**
     * Test listing tasks.
     */
    public function test_user_can_list_tasks(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Authentication
        $this->actingAs($user);
        
        // Create tasks for this user
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);
        
        // Send request
        $response = $this->getJson('/api/tasks');
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test updating task.
     */
    public function test_user_can_update_task(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Authentication
        $this->actingAs($user);
        
        // Create a task owned by this user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Update data
        $updateData = [
            'title' => 'Updated Title',
            'status' => 'in-progress',
        ];
        
        // Send request
        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('data.title', 'Updated Title')
                 ->assertJsonPath('data.status', 'in-progress');
        
        // Check database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in-progress',
        ]);
    }
    
    /**
     * Test deleting task.
     */
    public function test_user_can_delete_task(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Authentication
        $this->actingAs($user);
        
        // Create a task owned by this user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        
        // Send request
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        
        // Assert response
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success');
        
        // Check database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
    
    /**
     * Test users can only access their own tasks.
     */
    public function test_user_cannot_access_others_tasks(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Create a task owned by user2
        $task = Task::factory()->create([
            'user_id' => $user2->id,
        ]);
        
        // Authentication as user1
        $this->actingAs($user1);
        
        // Send request
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        // Assert user1 cannot access user2's task
        $response->assertStatus(403);
    }
}
