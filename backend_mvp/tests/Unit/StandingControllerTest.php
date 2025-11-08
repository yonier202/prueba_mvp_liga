<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\StandingController;
use App\Models\Team;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class StandingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $standingService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = Mockery::mock(StandingService::class);
        $this->controller = new StandingController($this->standingService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_standings_successfully(): void
    {
        // Arrange
        $team1 = Team::factory()->create([
            'name' => 'Team A',
            'points' => 10,
            'won' => 3,
        ]);

        $team2 = Team::factory()->create([
            'name' => 'Team B',
            'points' => 5,
            'won' => 1,
        ]);

        $expectedStandings = collect([$team1, $team2]);

        $this->standingService
            ->shouldReceive('standings')
            ->once()
            ->andReturn($expectedStandings);

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Tabla de posiciones obtenida correctamente', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('count', $responseData);
        $this->assertEquals(2, $responseData['count']);
        $this->assertCount(2, $responseData['data']);
    }

    public function test_index_returns_empty_standings(): void
    {
        // Arrange
        $this->standingService
            ->shouldReceive('standings')
            ->once()
            ->andReturn(collect([]));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(0, $responseData['count']);
        $this->assertEmpty($responseData['data']);
    }

    public function test_index_handles_exception(): void
    {
        // Arrange
        $this->standingService
            ->shouldReceive('standings')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al obtener la tabla de posiciones', $responseData['message']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function test_index_returns_correct_count(): void
    {
        // Arrange
        $teams = Team::factory()->count(5)->create();

        $this->standingService
            ->shouldReceive('standings')
            ->once()
            ->andReturn($teams);

        // Act
        $response = $this->controller->index();

        // Assert
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(5, $responseData['count']);
        $this->assertCount(5, $responseData['data']);
    }
}

