<?php

use App\Events\RoomUpdated;
use App\Events\testingEvent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\OperationalController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    //event(new testingEvent(25));
    event(new RoomUpdated(json_decode('{
        "id": "1",
        "name": "A",
        "private":"false",
        "status":"AGUARDANDO",
        "players": {
            "home": {},
            "visitant": {
                "id": "2",
                "name": "User 2",
                "skin": "0",
                "score": "0"
            }
        },
        "spectators": [
            {
                "id": "31",
                "name": "User 3",
                "skin": "0"
            },
            {
                "id": "4",
                "name": "User 4",
                "skin": "0"
            }
        ],
        "game_choosen": [
            "tic-tac-toe"
        ]
    }')));
    return 'done';
});
Route::get('/reset', [OperationalController::class, 'clearRooms']);



// Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
// Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
