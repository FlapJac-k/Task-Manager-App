<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId1 = User::where('email', 'user@example.com')->value('id');
        $userId2 = User::where('email', 'user2@example.com')->value('id');

        $repoTask = Task::create([
            'title' => 'Set up project repo',
            'description' => 'Initialize Git repository, add README, and push to GitHub.',
            'assigned_to' => $userId1,
            'due_date' => Carbon::now()->addDays(3),
            'status' => 'completed',
        ]);

        $schemaTask = Task::create([
            'title' => 'Design database schema',
            'description' => 'Plan out tables and relationships using an ERD tool.',
            'assigned_to' => $userId1,
            'due_date' => Carbon::now()->addDays(3),
            'status' => 'canceled',
        ]);

        $authTask = Task::create([
            'title' => 'Implement authentication',
            'description' => 'Use Laravel Sanctum to allow user login/logout via API.',
            'assigned_to' => $userId1,
            'due_date' => Carbon::now()->addDays(4),
            'status' => 'pending',

        ]);

        $testTask = Task::create([
            'title' => 'Write unit tests',
            'description' => 'Test TaskService, AuthService, and other business logic.',
            'assigned_to' => $userId1,
            'due_date' => Carbon::now()->addDays(5),
            'status' => 'inprogress',
        ]);

        $deployTask = Task::create([
            'title' => 'Deploy to staging',
            'description' => 'Push code to staging server and verify basic functionality.',
            'assigned_to' => $userId2,
            'due_date' => Carbon::now()->addDays(5),
            'status' => 'pending',
        ]);

        $authTask->dependencies()->attach([$repoTask->id, $schemaTask->id]);

        $testTask->dependencies()->attach($authTask->id);

        $deployTask->dependencies()->attach([$authTask->id, $testTask->id]);
    }
}
