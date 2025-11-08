<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Services\GameService;
use App\Services\StandingService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $standingService;
    protected $gameService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = Mockery::mock(StandingService::class);
        $this->gameService = new GameService($this->standingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_pending_returns_only_pending_games_with_teams(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create(['name' => 'Team A']);
        $awayTeam = Team::factory()->create(['name' => 'Team B']);

        $pendingGame = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $playedGame = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'played',
            'home_score' => 2,
            'away_score' => 1,
        ]);

        // Act
        $result = $this->gameService->listPending();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($pendingGame->id, $result->first()->id);
        $this->assertNotNull($result->first()->homeTeam);
        $this->assertNotNull($result->first()->awayTeam);
        $this->assertEquals('Team A', $result->first()->homeTeam->name);
        $this->assertEquals('Team B', $result->first()->awayTeam->name);
    }

    public function test_list_pending_returns_empty_collection_when_no_pending_games(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'played',
            'home_score' => 2,
            'away_score' => 1,
        ]);

        // Act
        $result = $this->gameService->listPending();

        // Assert
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_report_result_updates_game_and_calls_standing_service(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
            'home_score' => null,
            'away_score' => null,
        ]);

        $data = [
            'home_score' => 3,
            'away_score' => 1,
        ];

        $this->standingService
            ->shouldReceive('updateStandings')
            ->once()
            ->with(Mockery::on(function ($gameArg) use ($game) {
                return $gameArg->id === $game->id;
            }))
            ->andReturn(true);

        // Act
        $result = $this->gameService->reportResult($game->id, $data);

        // Assert
        $this->assertInstanceOf(Game::class, $result);
        $this->assertEquals(3, $result->home_score);
        $this->assertEquals(1, $result->away_score);
        $this->assertEquals('played', $result->status);

        $game->refresh();
        $this->assertEquals(3, $game->home_score);
        $this->assertEquals(1, $game->away_score);
        $this->assertEquals('played', $game->status);
    }

    public function test_report_result_throws_exception_when_game_not_found(): void
    {
        // Arrange
        $data = [
            'home_score' => 2,
            'away_score' => 1,
        ];

        $nonExistentId = 9999;

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Partido no encontrado con ID: {$nonExistentId}");

        $this->gameService->reportResult($nonExistentId, $data);
    }

    public function test_report_result_throws_exception_when_home_score_is_not_numeric(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $data = [
            'home_score' => 'not-a-number',
            'away_score' => 1,
        ];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Los puntajes deben ser valores numéricos');

        $this->gameService->reportResult($game->id, $data);
    }

    public function test_report_result_throws_exception_when_away_score_is_not_numeric(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $data = [
            'home_score' => 2,
            'away_score' => 'not-a-number',
        ];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Los puntajes deben ser valores numéricos');

        $this->gameService->reportResult($game->id, $data);
    }

    public function test_report_result_throws_exception_when_home_score_is_missing(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $data = [
            'away_score' => 1,
        ];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Los puntajes deben ser valores numéricos');

        $this->gameService->reportResult($game->id, $data);
    }

    public function test_report_result_accepts_zero_scores(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $data = [
            'home_score' => 0,
            'away_score' => 0,
        ];

        $this->standingService
            ->shouldReceive('updateStandings')
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->gameService->reportResult($game->id, $data);

        // Assert
        $this->assertEquals(0, $result->home_score);
        $this->assertEquals(0, $result->away_score);
        $this->assertEquals('played', $result->status);
    }
}

