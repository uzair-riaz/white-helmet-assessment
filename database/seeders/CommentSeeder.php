<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();
        
        if ($tasks->isEmpty() || $users->isEmpty()) {
            echo "Please run TaskSeeder first to create tasks and users.\n";
            return;
        }
        
        // Add random comments to tasks
        foreach ($tasks as $task) {
            // Add between 0 and 5 comments for each task
            $commentCount = rand(0, 5);
            
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::factory()->create([
                    'task_id' => $task->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
        
        // Add some specific comments from task owners
        $tasks->take(10)->each(function ($task) {
            Comment::factory()->create([
                'task_id' => $task->id, 
                'user_id' => $task->user_id,
                'content' => 'This is an update from the task creator.',
            ]);
        });
        
        // Add some specific comments from assigned users
        $assignedTasks = $tasks->filter(function ($task) {
            return $task->assigned_to !== null;
        });
        
        $assignedTasks->take(5)->each(function ($task) {
            Comment::factory()->create([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
                'content' => 'This is an update from the assigned person.',
            ]);
        });
    }
}
