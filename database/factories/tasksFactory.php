<?php

namespace Database\Factories;

use App\Models\projects;
use App\Models\tasks;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class tasksFactory extends Factory
{
    protected $model = tasks::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'type' => $this->faker->word(),
            'description' => $this->faker->text(),
            'status' => $this->faker->word(),
            'estimated_end_date' => Carbon::now(),
            'due_date' => Carbon::now(),
            'start_date' => Carbon::now(),
            'estimated_start_date' => Carbon::now(),
            'estimated_time_inDays' => $this->faker->randomNumber(),
            'actual_time_inDays' => $this->faker->randomNumber(),
            'priority' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => projects::factory(),
        ];
    }
}
