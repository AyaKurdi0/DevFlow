<?php

namespace Database\Factories;

use App\Models\review;
use App\Models\tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class reviewFactory extends Factory
{
    protected $model = review::class;

    public function definition(): array
    {
        return [
            'comment' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'task_id' => tasks::factory(),
            'leader_id' => User::factory(),
        ];
    }
}
