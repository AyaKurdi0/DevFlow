<?php

namespace Database\Factories;

use App\Models\user_task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class user_taskFactory extends Factory
{
    protected $model = user_task::class;

    public function definition(): array
    {
        return [
            'task_id' => $this->faker->randomNumber(),
            'developer_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
