<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $teams = Team::factory()->count(4)->create();

        Game::create([
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
        ]);

        Game::create([
            'home_team_id' => $teams[2]->id,
            'away_team_id' => $teams[3]->id,
        ]);
    }
}
