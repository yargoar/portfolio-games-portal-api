<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Managers\GameRoomManager;
use Illuminate\Support\Facades\Log;
use App\Events\RoomUpdated;

class GameRoomController extends Controller
{
    public function index()
    {
        $rooms = GameRoomManager::getRooms();

        return response()->json($rooms);
    }

    public function join(Request $request)
    {
        $request->validate([
            'roomId' => 'required|integer',
            'user' => 'required|integer',
            'userName' => 'required|string',
            'position' => 'required|integer',
            'isSpectator' => 'required|boolean'
        ]);

        try {
            $room = GameRoomManager::joinRoom(
                $request->roomId,
                $request->user,
                $request->userName,
                $request->position,
                $request->isSpectator
            );
            Log::info("passou no controller tranquilamente depois do GameRoomManager::joinRoom");

              return response()->json($room);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function leave(Request $request)
    {
        $request->validate([
            'user' => 'required|integer',
            'roomId' => 'required|integer',
            'position' => 'required|integer',
        ]);

        try {
            $room = GameRoomManager::leaveRoom(
                $request->roomId,
                $request->user,
                $request->position,
            );

            // Broadcast para todos na sala via WebSocket
            broadcast(new \App\Events\RoomUpdated($room));

            return response()->json($room);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
