<?php

namespace Database\Factories;

use App\Models\specialization;
use App\Models\Team;
use App\Models\Team_Members;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class Team_MembersFactory extends Factory
{
    protected $model = Team_Members::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'team_id' => Team::factory(),
            'developer_id' => User::factory(),
            'specialization_id' => specialization::factory(),
        ];
    }
}
