<?php

namespace Database\Factories;

use App\Models\user_notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class user_notificationFactory extends Factory
{
    protected $model = user_notification::class;

    public function definition(): array
    {
        return [
            'receiver_id' => $this->faker->randomNumber(),
            'notification_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
