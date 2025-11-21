<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\ProjectUser;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ------------------------------------
        // 1. Create 10 real users
        // ------------------------------------
        $realUsers = [
            'Ali Raza',
            'Ayesha Khan',
            'Bilal Ahmed',
            'Fatima Tariq',
            'Hassan Javed',
            'Maria Idrees',
            'Saad Malik',
            'Sana Tanveer',
            'Usman Khalid',
            'Zoya Sheikh',
        ];

        $users = collect();

        foreach ($realUsers as $name) {
            $users->push(User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)).'@example.com',
                'password' => Hash::make('password123'),
                'avatar_url' => null,
                'role' => 'member',
            ]));
        }

        // ------------------------------------
        // 2. 20 real project names
        // ------------------------------------
        $projectNames = [
            'Inventory System', 'CRM Dashboard', 'Analytics Suite', 'Task Manager',
            'Booking Engine', 'Reporting Hub', 'Payroll Module', 'Messaging API',
            'Blog Engine', 'Finance Tracker', 'Attendance Portal', 'Restaurant App',
            'Portfolio Builder', 'Scheduler App', 'Snippet Manager', 'Feedback Center',
            'Learning Portal', 'HR Control Panel', 'Project Roadmap', 'Issue Tracker',
        ];

        $projectIndex = 0;
        $allTasks = collect();

        // ------------------------------------
        // 3. Each user gets 2 projects
        // ------------------------------------
        foreach ($users as $user) {

            for ($i = 0; $i < 2; $i++) {

                $project = Project::create([
                    'owner_id' => $user->id,
                    'name' => $projectNames[$projectIndex++],
                    'description' => "This project is owned by {$user->name}.",
                    'status' => 'active',
                ]);

                // ------------------------------------
                // 4. Create 5–7 tasks manually
                // ------------------------------------
                $taskTitles = [
                    'Setup environment',
                    'Fix UI inconsistencies',
                    'Integrate API endpoints',
                    'Write test cases',
                    'Refactor modules',
                    'Improve performance',
                    'Review pull requests',
                ];

                $taskCount = rand(5,7);

                for ($t = 0; $t < $taskCount; $t++) {

                    $task = Task::create([
                        'project_id' => $project->id,
                        'assigned_to' => $users->random()->id,
                        'title' => $taskTitles[$t],
                        'description' => "Task {$t} for project {$project->name}.",
                        'priority' => ['low', 'medium', 'high'][rand(0,2)],
                        'status' => ['todo', 'in_progress', 'done'][rand(0,2)],
                        'due_date' => now()->addDays(rand(3, 30)),
                    ]);

                    $allTasks->push($task);
                }

                // ------------------------------------
                // 5. Add project members (2–4 users)
                // ------------------------------------
                $memberIds = $users->where('id', '!=', $user->id)
                                   ->random(rand(2,4))
                                   ->pluck('id');

                foreach ($memberIds as $memberId) {
                    ProjectUser::create([
                        'project_id' => $project->id,
                        'user_id' => $memberId,
                        'role' => ['viewer', 'member', 'manager'][rand(0,2)],
                    ]);
                }

                // ------------------------------------
                // 6. Activity logs (real and NOT NULL)
                // ------------------------------------
                $actions = [
                    'created the project',
                    'updated project details',
                    'added new tasks',
                    'reviewed task progress',
                    'assigned tasks to members',
                ];

                // pick tasks from this project only
                $projectTasks = $allTasks->where('project_id', $project->id);

                foreach ($actions as $action) {
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'project_id' => $project->id,
                        'task_id' => $projectTasks->random()->id, // NO NULLS
                        'action' => "{$user->name} {$action}.",
                        'metadata' => [
                            'source' => 'seed',
                            'ip' => request()->ip() ?? '127.0.0.1',
                        ],
                    ]);
                }
            }
        }

        // ------------------------------------
        // 7. Exactly 30 real comments
        // ------------------------------------
        $commentLines = [
            'Looks good.',
            'Please update this.',
            'This needs review.',
            'Working on it now.',
            'Found an issue here.',
            'Tested and verified.',
            'Handled in the latest push.',
            'Recheck after deployment.',
            'Need backend support.',
            'Approved.',
        ];

        for ($i = 0; $i < 30; $i++) {
            $task = $allTasks->random();

            Comment::create([
                'task_id' => $task->id,
                'user_id' => $users->random()->id,
                'comment' => $commentLines[array_rand($commentLines)],
            ]);
        }
    }
}
