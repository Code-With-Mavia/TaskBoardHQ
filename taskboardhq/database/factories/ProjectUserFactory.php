<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;

class ProjectUserFactory extends Factory
{
    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(['viewer','member','manager']),
            'added_at' => now(),
        ];
    }
}
