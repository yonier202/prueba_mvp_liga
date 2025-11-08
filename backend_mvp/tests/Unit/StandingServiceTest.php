<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Services\StandingService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StandingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $standingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = new StandingService();
    }

    public function test_update_standings_increments_played_for_both_teams(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 2,
            'away_score' => 1,
            'status' => 'played',
        ]);

        // Act
        $result = $this->standingService->updateStandings($game);

        // Assert
        $this->assertTrue($result);

        $homeTeam->refresh();
        $awayTeam->refresh();

        $this->assertEquals(1, $homeTeam->played);
        $this->assertEquals(1, $awayTeam->played);
    }

    public function test_update_standings_handles_home_team_win(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 3,
            'away_score' => 1,
            'status' => 'played',
        ]);

        // Act
        $this->standingService->updateStandings($game);

        // Assert
        $homeTeam->refresh();
        $awayTeam->refresh();

        $this->assertEquals(1, $homeTeam->won);
        $this->assertEquals(0, $homeTeam->drawn);
        $this->assertEquals(0, $homeTeam->lost);
        $this->assertEquals(3, $homeTeam->points);

        $this->assertEquals(0, $awayTeam->won);
        $this->assertEquals(0, $awayTeam->drawn);
        $this->assertEquals(1, $awayTeam->lost);
        $this->assertEquals(0, $awayTeam->points);
    }

    public function test_update_standings_handles_away_team_win(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 1,
            'away_score' => 2,
            'status' => 'played',
        ]);

        // Act
        $this->standingService->updateStandings($game);

        // Assert
        $homeTeam->refresh();
        $awayTeam->refresh();

        $this->assertEquals(0, $homeTeam->won);
        $this->assertEquals(0, $homeTeam->drawn);
        $this->assertEquals(1, $homeTeam->lost);
        $this->assertEquals(0, $homeTeam->points);

        $this->assertEquals(1, $awayTeam->won);
        $this->assertEquals(0, $awayTeam->drawn);
        $this->assertEquals(0, $awayTeam->lost);
        $this->assertEquals(3, $awayTeam->points);
    }

    public function test_update_standings_handles_draw(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 2,
            'away_score' => 2,
            'status' => 'played',
        ]);

        // Act
        $this->standingService->updateStandings($game);

        // Assert
        $homeTeam->refresh();
        $awayTeam->refresh();

        $this->assertEquals(0, $homeTeam->won);
        $this->assertEquals(1, $homeTeam->drawn);
        $this->assertEquals(0, $homeTeam->lost);
        $this->assertEquals(1, $homeTeam->points);

        $this->assertEquals(0, $awayTeam->won);
        $this->assertEquals(1, $awayTeam->drawn);
        $this->assertEquals(0, $awayTeam->lost);
        $this->assertEquals(1, $awayTeam->points);
    }

    public function test_update_standings_accumulates_multiple_games(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 2,
            'won' => 1,
            'drawn' => 1,
            'lost' => 0,
            'points' => 4,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 1,
            'won' => 0,
            'drawn' => 0,
            'lost' => 1,
            'points' => 0,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => 3,
            'away_score' => 0,
            'status' => 'played',
        ]);

        // Act
        $this->standingService->updateStandings($game);

        // Assert
        $homeTeam->refresh();
        $awayTeam->refresh();

        $this->assertEquals(3, $homeTeam->played);
        $this->assertEquals(2, $homeTeam->won);
        $this->assertEquals(1, $homeTeam->drawn);
        $this->assertEquals(0, $homeTeam->lost);
        $this->assertEquals(7, $homeTeam->points);

        $this->assertEquals(2, $awayTeam->played);
        $this->assertEquals(0, $awayTeam->won);
        $this->assertEquals(0, $awayTeam->drawn);
        $this->assertEquals(2, $awayTeam->lost);
        $this->assertEquals(0, $awayTeam->points);
    }

    public function test_update_standings_throws_exception_when_teams_not_loaded(): void
    {
        // Arrange
        $game = new Game([
            'home_team_id' => 1,
            'away_team_id' => 2,
            'home_score' => 2,
            'away_score' => 1,
            'status' => 'played',
        ]);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se pudieron cargar los equipos del partido');

        $this->standingService->updateStandings($game);
    }

    public function test_update_standings_throws_exception_when_scores_are_null(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => null,
            'away_score' => null,
            'status' => 'pending',
        ]);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Los puntajes del partido no pueden ser nulos');

        $this->standingService->updateStandings($game);
    }

    public function test_update_standings_rolls_back_on_exception(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create([
            'played' => 5,
            'won' => 2,
            'drawn' => 1,
            'lost' => 2,
            'points' => 7,
        ]);

        $awayTeam = Team::factory()->create([
            'played' => 3,
            'won' => 1,
            'drawn' => 1,
            'lost' => 1,
            'points' => 4,
        ]);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => null,
            'away_score' => null,
            'status' => 'pending',
        ]);

        $initialHomePlayed = $homeTeam->played;
        $initialHomeWon = $homeTeam->won;
        $initialHomePoints = $homeTeam->points;
        $initialAwayPlayed = $awayTeam->played;
        $initialAwayWon = $awayTeam->won;
        $initialAwayPoints = $awayTeam->points;

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Los puntajes del partido no pueden ser nulos');

        try {
            $this->standingService->updateStandings($game);
        } catch (Exception $e) {
            // Assert - Verificar que los datos no se modificaron debido al rollback
            $homeTeam->refresh();
            $awayTeam->refresh();

            $this->assertEquals($initialHomePlayed, $homeTeam->played);
            $this->assertEquals($initialHomeWon, $homeTeam->won);
            $this->assertEquals($initialHomePoints, $homeTeam->points);
            $this->assertEquals($initialAwayPlayed, $awayTeam->played);
            $this->assertEquals($initialAwayWon, $awayTeam->won);
            $this->assertEquals($initialAwayPoints, $awayTeam->points);

            throw $e;
        }
    }

    public function test_standings_returns_teams_ordered_by_points_and_won(): void
    {
        // Arrange
        $team1 = Team::factory()->create([
            'name' => 'Team A',
            'points' => 10,
            'won' => 3,
        ]);

        $team2 = Team::factory()->create([
            'name' => 'Team B',
            'points' => 10,
            'won' => 4,
        ]);

        $team3 = Team::factory()->create([
            'name' => 'Team C',
            'points' => 5,
            'won' => 1,
        ]);

        // Act
        $result = $this->standingService->standings();

        // Assert
        $this->assertCount(3, $result);
        $this->assertEquals('Team B', $result->first()->name); // Más puntos y más victorias
        $this->assertEquals('Team A', $result->get(1)->name);
        $this->assertEquals('Team C', $result->last()->name);
    }

    public function test_standings_returns_empty_collection_when_no_teams(): void
    {
        // Act
        $result = $this->standingService->standings();

        // Assert
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_standings_returns_collection_even_on_error(): void
    {
        // Arrange - Crear algunos equipos primero
        Team::factory()->count(2)->create();

        // Act - El método standings debe manejar errores internos
        $result = $this->standingService->standings();

        // Assert - Debe retornar una colección (puede estar vacía si hay error)
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }
}

