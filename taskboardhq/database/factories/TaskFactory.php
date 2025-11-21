<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'assigned_to' => rand(0,1) ? User::factory() : null,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low','medium','high']),
            'status' => $this->faker->randomElement(['todo','in_progress','done']),
            'due_date' => $this->faker->optional()->date(),
        ];
    }
}
