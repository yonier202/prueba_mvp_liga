<?php

namespace App\Services;

use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamService
{
     public function list()
    {
        try {
            return Team::all();
        } catch (Exception $e) {
            Log::error('Error al listar equipos: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function create(array $data)
    {
        try {
            if (empty($data['name'])) {
                throw new Exception("El nombre del equipo es requerido");
            }

            return Team::create($data);
        } catch (Exception $e) {
            Log::error('Error al crear equipo: ' . $e->getMessage(), [
                'data' => $data
            ]);
            throw $e;
        }
    }
}
