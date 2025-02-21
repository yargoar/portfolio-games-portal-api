<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomsController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\GameRoomController;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

// Definindo o rate limiter
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60);  // Por exemplo, 60 requisições por minuto
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/get_rooms', [RoomsController::class, 'getRooms']);
Route::get('/update_room', [RoomsController::class, 'updateRoom']);

Route::get('/rooms', [GameRoomController::class, 'index']);
Route::post('/rooms/join', [GameRoomController::class, 'join']);
Route::post('/rooms/leave', [GameRoomController::class, 'leave']);

Route::post('/make_a_move', [MatchController::class, 'makeAMove']);
Route::get('/get_all_moves/{tableId}', [MatchController::class, 'getAllMoves']);
Route::get('/get_last_move/{tableId}', [MatchController::class, 'getLastMove']);
Route::post('/send_a_message', [MatchController::class, 'sendAMessage']);