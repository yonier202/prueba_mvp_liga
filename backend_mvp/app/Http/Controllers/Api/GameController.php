<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameStoreRequest;
use App\Http\Requests\ReportResultRequest;
use App\Services\GameService;
use Exception;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    protected $service;

    public function __construct(GameService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        try {
            $games = $this->service->listPending();

            return response()->json([
                'success' => true,
                'data' => $games,
                'message' => 'Partidos pendientes obtenidos correctamente'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en GameController@index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los partidos pendientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(GameStoreRequest $request): JsonResponse {
        try {
            $game = $this->service->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $game,
                'message' => 'Partido creado correctamente'
            ], 201);
        } catch (Exception $e) {
            Log::error('Error en GameController@store: ' . $e->getMessage(), [
                'request_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el partido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reportResult($id, ReportResultRequest $request): JsonResponse
    {
        try {
            $game = $this->service->reportResult($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $game,
                'message' => 'Resultado reportado correctamente'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en GameController@reportResult: ' . $e->getMessage(), [
                'game_id' => $id,
                'request_data' => $request->validated()
            ]);

            $statusCode = str_contains($e->getMessage(), 'no encontrado') ? 404 : 500;

            return response()->json([
                'success' => false,
                'message' => 'Error al reportar el resultado',
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}
