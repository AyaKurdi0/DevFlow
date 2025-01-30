<?php

namespace Database\Factories;

use App\Models\documents;
use App\Models\tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class documentsFactory extends Factory
{
    protected $model = documents::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'document_type' => $this->faker->word(),
            'path' => $this->faker->word(),
            'uploaded_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'task_id' => tasks::factory(),
            'uploaded_by' => User::factory(),
        ];
    }
}
