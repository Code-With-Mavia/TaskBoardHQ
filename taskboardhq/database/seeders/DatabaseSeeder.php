<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\ActivityLog;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // Create Users
        $users = User::factory()->count(5)->create();

        // Create Projects
        $projects = Project::factory()->count(3)->create();

        foreach ($projects as $project) {

            // Assign users to project (pivot)
            $members = $users->random(rand(2, 3));
            $project->users()->attach($members);

            // Log membership creation
            foreach ($members as $member) {
                ActivityLog::create([
                    'user_id' => $member->id,
                    'action' => 'PROJECT_MEMBER_ADDED',
                    'project_id' => $project->id,
                    'task_id' => null,
                    'description' => "User {$member->id} added to project {$project->id}"
                ]);
            }

            // Create tasks
            $tasks = Task::factory()->count(6)->create([
                'project_id' => $project->id,
                'assigned_to' => $members->random()->id,
            ]);

            foreach ($tasks as $task) {

                // Log task creation
                ActivityLog::create([
                    'user_id' => $task->assigned_to,
                    'action' => 'TASK_CREATED',
                    'project_id' => $project->id,
                    'task_id' => $task->id,
                    'description' => "Task {$task->id} created in project {$project->id}"
                ]);

                // Add comments
                $commenters = $members->random(rand(1, 2));

                foreach ($commenters as $user) {
                    $comment = Comment::create([
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'comment' => fake()->sentence(),
                    ]);

                    // Log comment creation
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'COMMENT_ADDED',
                        'project_id' => $project->id,
                        'task_id' => $task->id,
                        'description' => "Comment {$comment->id} added to task {$task->id}"
                    ]);
                }
            }
        }
    }
}
