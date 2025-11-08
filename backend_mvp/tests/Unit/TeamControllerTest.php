<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\TeamController;
use App\Http\Requests\StoreTeamRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $teamService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamService = Mockery::mock(TeamService::class);
        $this->controller = new TeamController($this->teamService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_teams_successfully(): void
    {
        // Arrange
        $team1 = Team::factory()->create(['name' => 'Team A']);
        $team2 = Team::factory()->create(['name' => 'Team B']);

        $expectedTeams = collect([$team1, $team2]);

        $this->teamService
            ->shouldReceive('list')
            ->once()
            ->andReturn($expectedTeams);

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Equipos obtenidos correctamente', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertCount(2, $responseData['data']);
    }

    public function test_index_returns_empty_array_when_no_teams(): void
    {
        // Arrange
        $this->teamService
            ->shouldReceive('list')
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
        $this->teamService
            ->shouldReceive('list')
            ->once()
            ->andThrow(new \Exception('Database error'));

        // Act
        $response = $this->controller->index();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al obtener los equipos', $responseData['message']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function test_store_creates_team_successfully(): void
    {
        // Arrange
        $requestData = [
            'name' => 'New Team',
        ];

        $team = Team::factory()->make($requestData);
        $team->id = 1;

        $request = Mockery::mock(StoreTeamRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn($requestData);

        $this->teamService
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($team);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Equipo creado correctamente', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals('New Team', $responseData['data']['name']);
    }

    public function test_store_handles_exception(): void
    {
        // Arrange
        $requestData = [
            'name' => '',
        ];

        $request = Mockery::mock(StoreTeamRequest::class);
        $request->shouldReceive('validated')
            ->andReturn($requestData);

        $this->teamService
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andThrow(new \Exception('El nombre del equipo es requerido'));

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Error al crear el equipo', $responseData['message']);
        $this->assertArrayHasKey('error', $responseData);
    }

    public function test_store_validates_request_data(): void
    {
        // Arrange
        $requestData = [
            'name' => 'Valid Team Name',
        ];

        $team = Team::factory()->make($requestData);
        $team->id = 1;

        $request = Mockery::mock(StoreTeamRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn($requestData);

        $this->teamService
            ->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($team);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }
}

