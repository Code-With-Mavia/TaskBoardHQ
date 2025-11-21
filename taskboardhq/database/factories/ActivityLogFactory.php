<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class ActivityLogFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'project_id' => rand(0,1) ? Project::factory() : null,
            'task_id' => rand(0,1) ? Task::factory() : null,
            'action' => $this->faker->randomElement([
                'created_project',
                'updated_project',
                'created_task',
                'updated_task',
                'added_comment'
            ]),
            'metadata' => [
                'ip' => $this->faker->ipv4(),
                'note' => $this->faker->sentence(),
            ],
        ];
    }
}
