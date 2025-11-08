<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\GameController;
use App\Http\Requests\ReportResultRequest;
use App\Models\Game;
use App\Models\Team;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $gameService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gameService = Mockery::mock(GameService::class);
        $this->controller = new GameController($this->gameService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_pending_games_successfully(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create(['name' => 'Team A']);
        $awayTeam = Team::factory()->create(['name' => 'Team B']);

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $expectedGames = collect([$game->load(['homeTeam', 'awayTeam'])]);

        $this->gameService
            ->shouldReceive('listPending')
            ->once()
            ->andReturn($expectedGames);

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Partidos pendientes obtenidos correctamente', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertCount(1, $responseData['data']);
    }

    public function test_index_returns_empty_array_when_no_pending_games(): void
    {
        // Arrange
        $this->gameService
            ->shouldReceive('listPending')
            ->once()
            ->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEmpty($responseData['data']);
    }

    public function test_index_handles_exception(): void
    {
        // Arrange
        $this->gameService
            ->shouldReceive('listPending')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al obtener los partidos pendientes', $responseData['message']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function test_report_result_returns_success_response(): void
    {
        // Arrange
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'status' => 'pending',
        ]);

        $requestData = [
            'home_score' => 3,
            'away_score' => 1,
        ];

        $updatedGame = $game->replicate();
        $updatedGame->home_score = 3;
        $updatedGame->away_score = 1;
        $updatedGame->status = 'played';

        $request = Mockery::mock(ReportResultRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn($requestData);

        $this->gameService
            ->shouldReceive('reportResult')
            ->once()
            ->with($game->id, $requestData)
            ->andReturn($updatedGame);

        // Act
        $response = $this->controller->reportResult($game->id, $request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Resultado reportado correctamente', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
    }

    public function test_report_result_returns_404_when_game_not_found(): void
    {
        // Arrange
        $requestData = [
            'home_score' => 3,
            'away_score' => 1,
        ];

        $request = Mockery::mock(ReportResultRequest::class);
        $request->shouldReceive('validated')
            ->andReturn($requestData);


        $this->gameService
            ->shouldReceive('reportResult')
            ->once()
            ->with(999, $requestData)
            ->andThrow(new \Exception('Partido no encontrado con ID: 999'));

        // Act
        $response = $this->controller->reportResult(999, $request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al reportar el resultado', $responseData['message']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function test_report_result_returns_500_on_other_errors(): void
    {
        // Arrange
        $requestData = [
            'home_score' => 3,
            'away_score' => 1,
        ];

        $request = Mockery::mock(ReportResultRequest::class);
        $request->shouldReceive('validated')
            ->andReturn($requestData);

        $this->gameService
            ->shouldReceive('reportResult')
            ->once()
            ->with(1, $requestData)
            ->andThrow(new \Exception('Validation error'));

        // Act
        $response = $this->controller->reportResult(1, $request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al reportar el resultado', $responseData['message']);
    }
}

