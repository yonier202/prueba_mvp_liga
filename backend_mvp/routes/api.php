<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    TeamController,
    GameController,
    StandingController
};

Route::get('/teams', [TeamController::class, 'index']);
Route::post('/teams', [TeamController::class, 'store']);

Route::get('/games', [GameController::class, 'index']);
Route::post('/games/{id}/result', [GameController::class, 'reportResult']);

Route::get('/standings', [StandingController::class, 'index']);
