<?php

namespace App\Services;

use App\Models\Game;
use App\Services\StandingService;
use Exception;
use Illuminate\Support\Facades\Log;

class GameService
{
    protected $standingService;

    public function __construct(StandingService $standingService)
    {
        $this->standingService = $standingService;
    }

    public function listPending()
    {
        return Game::with(['homeTeam', 'awayTeam'])
            ->where('status', 'pending')
            ->get();
    }
    public function create(array $data)
    {
        try {
            $game = Game::create([
                'home_team_id' => $data['home_team_id'],
                'away_team_id' => $data['away_team_id'],
            ]);

            return $game;
        } catch (Exception $e) {
            Log::error('Error al crear el partido: ' . $e->getMessage(), [
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function reportResult($gameId, array $data)
    {
        try {
            $game = Game::find($gameId);

            if (!$game) {
                throw new Exception("Partido no encontrado con ID: {$gameId}");
            }

            if (!is_numeric($data['home_score'] ?? null) || !is_numeric($data['away_score'] ?? null)) {
                throw new Exception("Los puntajes deben ser valores numéricos");
            }

            $game->update([
                'home_score' => $data['home_score'],
                'away_score' => $data['away_score'],
                'status' => 'played',
            ]);

            $this->standingService->updateStandings($game);

            return $game;
        } catch (Exception $e) {
            Log::error('Error al reportar resultado del partido: ' . $e->getMessage(), [
                'game_id' => $gameId,
                'data' => $data
            ]);
            throw $e;
        }
    }
}

