<?php

namespace App\Http\Controllers\Api;

use App\Events\RoomUpdated;
use App\Http\Controllers\Controller;
use App\Managers\GameRoomManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    public function updateRoom(Request $request)
    {
        $data = [
            "id" => "1",
            "name" => "A",
            "private" => "false",
            "status" => "AGUARDANDO",
            "players" => [
                "home" => [],
                "visitant" => [
                    "id" => "2",
                    "name" => "User 2",
                    "skin" => "0",
                    "score" => "0"
                ]
            ],
            "spectators" => [
                [
                    "id" => "31",
                    "name" => "User 3",
                    "skin" => "0"
                ],
                [
                    "id" => "4",
                    "name" => "User 4",
                    "skin" => "0"
                ]
            ],
            "game" => [
                "tic-tac-toe"
            ]
        ];
        $room = new RoomUpdated($data);
        event($room);
        // Adicionando um log para garantir que o evento foi disparado
        Log::info('RoomUpdated event triggered.', ['data' => $data]);
        return response()->json(['status' => 'success']);
    }

    public function getRooms(Request $request)
    {
        return GameRoomManager::getRooms();
    }
}
