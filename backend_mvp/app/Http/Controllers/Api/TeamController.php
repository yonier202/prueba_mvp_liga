<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Services\TeamService;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    protected $service;

    public function __construct(TeamService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $teams = $this->service->list();

            return response()->json([
                'success' => true,
                'data' => $teams,
                'message' => 'Equipos obtenidos correctamente'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en TeamController@index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los equipos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreTeamRequest $request)
    {
        try {
            $team = $this->service->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $team,
                'message' => 'Equipo creado correctamente'
            ], 201);
        } catch (Exception $e) {
            Log::error('Error en TeamController@store: ' . $e->getMessage(), [
                'request_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
