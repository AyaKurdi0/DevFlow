<?php

namespace Database\Factories;

use App\Models\Notifications;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NotificationsFactory extends Factory
{
    protected $model = Notifications::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'title' => $this->faker->word(),
            'content' => $this->faker->word(),
            'read_state' => $this->faker->word(),
            'receive_state' => $this->faker->word(),
            'sent_at' => Carbon::now(),
            'receive_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
