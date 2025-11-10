<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'home_score' => null,
            'away_score' => null,
            'status' => 'pending',
        ];
    }

    public function played(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'played',
            'home_score' => fake()->numberBetween(0, 5),
            'away_score' => fake()->numberBetween(0, 5),
        ]);
    }
}

