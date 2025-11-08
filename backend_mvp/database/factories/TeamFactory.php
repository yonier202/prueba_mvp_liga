<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Team;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'played' => 3,
            'won' => 1,
            'drawn' => 1,
            'lost' => 1,
            'points' => 4,
        ];
    }
}
