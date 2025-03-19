<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user for testing
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        
        // Create regular user for testing
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);
        
        // Seed tasks and comments
        $this->call([
            TaskSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
