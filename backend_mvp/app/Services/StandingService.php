<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StandingService
{
    public function updateStandings(Game $game)
    {
        DB::beginTransaction();

        try {
            $home = $game->homeTeam;
            $away = $game->awayTeam;

            if (!$home || !$away) {
                throw new Exception("No se pudieron cargar los equipos del partido");
            }

            if (is_null($game->home_score) || is_null($game->away_score)) {
                throw new Exception("Los puntajes del partido no pueden ser nulos");
            }

            // Partidos jugados
            $home->played += 1;
            $away->played += 1;

            // Resultado
            if ($game->home_score > $game->away_score) {
                $home->won += 1;
                $away->lost += 1;
                $home->points += 3;
            } elseif ($game->home_score < $game->away_score) {
                $away->won += 1;
                $home->lost += 1;
                $away->points += 3;
            } else {
                $home->drawn += 1;
                $away->drawn += 1;
                $home->points += 1;
                $away->points += 1;
            }

            $home->save();
            $away->save();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar standings: ' . $e->getMessage(), [
                'game_id' => $game->id,
                'home_team_id' => $game->home_team_id,
                'away_team_id' => $game->away_team_id
            ]);

            throw $e;
        }
    }

    public function standings()
    {
        try {
            $standings = Team::orderBy('points', 'desc')
                ->orderBy('won', 'desc')
                ->get();

            if ($standings->isEmpty()) {
                Log::warning('No hay equipos registrados en la base de datos');
            }

            return $standings;
        } catch (Exception $e) {
            Log::error('Error al obtener standings: ' . $e->getMessage());

            return collect([]);
        }
    }
}
