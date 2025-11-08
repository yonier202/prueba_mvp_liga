<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StandingService;
use Exception;
use Illuminate\Support\Facades\Log;

class StandingController extends Controller
{
    protected $service;

    public function __construct(StandingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $standings = $this->service->standings();

            return response()->json([
                'success' => true,
                'data' => $standings,
                'message' => 'Tabla de posiciones obtenida correctamente',
                'count' => $standings->count()
            ], 200);
        } catch (Exception $e) {
            Log::error('Error en StandingController@index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la tabla de posiciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
