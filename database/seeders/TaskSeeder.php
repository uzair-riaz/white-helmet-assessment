<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users for tasks if none exist
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }
        
        $users = User::all();
        
        // Create 20 tasks with random status
        Task::factory()
            ->count(20)
            ->state(function () use ($users) {
                return [
                    'user_id' => $users->random()->id,
                ];
            })
            ->create();
        
        // Create 10 assigned tasks
        Task::factory()
            ->count(10)
            ->state(function () use ($users) {
                $taskOwner = $users->random();
                $assignedUser = $users->where('id', '!=', $taskOwner->id)->random();
                
                return [
                    'user_id' => $taskOwner->id,
                    'assigned_to' => $assignedUser->id,
                ];
            })
            ->create();
            
        // Create 5 completed tasks
        Task::factory()
            ->count(5)
            ->completed()
            ->state(function () use ($users) {
                return [
                    'user_id' => $users->random()->id,
                ];
            })
            ->create();
            
        // Create 5 in-progress tasks
        Task::factory()
            ->count(5)
            ->inProgress()
            ->state(function () use ($users) {
                return [
                    'user_id' => $users->random()->id,
                ];
            })
            ->create();
    }
}
