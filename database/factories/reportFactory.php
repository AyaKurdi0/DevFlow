<?php

namespace Database\Factories;

use App\Models\report;
use App\Models\tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class reportFactory extends Factory
{
    protected $model = report::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'content' => $this->faker->word(),
            'Report_date' => Carbon::now(),
            'Report_time' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'task_id' => tasks::factory(),
            'developer_id' => User::factory(),
        ];
    }
}
