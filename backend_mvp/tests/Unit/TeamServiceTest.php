<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Services\TeamService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $teamService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamService = new TeamService();
    }

    public function test_list_returns_all_teams(): void
    {
        // Arrange
        $team1 = Team::factory()->create(['name' => 'Team A']);
        $team2 = Team::factory()->create(['name' => 'Team B']);
        $team3 = Team::factory()->create(['name' => 'Team C']);

        // Act
        $result = $this->teamService->list();

        // Assert
        $this->assertCount(3, $result);
        $this->assertTrue($result->contains($team1));
        $this->assertTrue($result->contains($team2));
        $this->assertTrue($result->contains($team3));
    }

    public function test_list_returns_empty_collection_when_no_teams(): void
    {
        // Act
        $result = $this->teamService->list();

        // Assert
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_create_creates_team_successfully(): void
    {
        // Arrange
        $data = [
            'name' => 'New Team',
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ];

        // Act
        $result = $this->teamService->create($data);

        // Assert
        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals('New Team', $result->name);
        $this->assertDatabaseHas('teams', [
            'name' => 'New Team',
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
        ]);
    }

    public function test_create_creates_team_with_only_name(): void
    {
        // Arrange
        $data = [
            'name' => 'Team Name Only',
        ];

        // Act
        $result = $this->teamService->create($data);

        // Assert
        $this->assertInstanceOf(Team::class, $result);
        $this->assertEquals('Team Name Only', $result->name);
        $this->assertDatabaseHas('teams', [
            'name' => 'Team Name Only',
        ]);
    }

    public function test_create_throws_exception_when_name_is_empty(): void
    {
        // Arrange
        $data = [
            'name' => '',
        ];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El nombre del equipo es requerido');

        $this->teamService->create($data);
    }

    public function test_create_throws_exception_when_name_is_missing(): void
    {
        // Arrange
        $data = [];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El nombre del equipo es requerido');

        $this->teamService->create($data);
    }

    public function test_create_throws_exception_when_name_is_null(): void
    {
        // Arrange
        $data = [
            'name' => null,
        ];

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El nombre del equipo es requerido');

        $this->teamService->create($data);
    }

    public function test_create_allows_statistics_in_data(): void
    {
        // Arrange
        $data = [
            'name' => 'Team With Stats',
            'played' => 5,
            'won' => 3,
            'drawn' => 1,
            'lost' => 1,
            'points' => 10,
        ];

        // Act
        $result = $this->teamService->create($data);

        // Assert
        $this->assertEquals('Team With Stats', $result->name);
        $this->assertEquals(5, $result->played);
        $this->assertEquals(3, $result->won);
        $this->assertEquals(1, $result->drawn);
        $this->assertEquals(1, $result->lost);
        $this->assertEquals(10, $result->points);
    }

    public function test_list_returns_collection_even_on_error(): void
    {
        // Arrange - Crear algunos equipos primero
        Team::factory()->count(2)->create();

        // Act - El método list debe manejar errores internos
        $result = $this->teamService->list();

        // Assert - Debe retornar una colección (puede estar vacía si hay error)
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }
}

