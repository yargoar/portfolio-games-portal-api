<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Managers\GameRoomManager;
use Illuminate\Http\Request;
use App\Managers\MatchManager;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MatchController extends Controller
{
    public function makeAMove(Request $request)
    {
        $request->validate([
            'type' => 'required',
                                    'string',
                                    Rule::in([
                                        "PLAYER_MOVE",
                                        "GAME_START",
                                        "GAME_OVER",
                                        "SPECTATOR_JOINED",
                                        "SPECTATOR_LEAVE",
                                        "PLAYER_LEAVE",
                                        "SEND_MESSAGE",
                                    ]),
            'payload' => 'required|array',
            'payload.player' => 'required|array',
            'payload.player.id' => 'required|integer',
            'payload.player.name' => 'required|string',
            'payload.move' => 'required|array',
            'payload.move.type' => 'required|string',
            'payload.tableId' => 'required|integer',
        ]);

        try {
            $move = MatchManager::makeAMove($request->all());

            $type = $move[0]["type"];
            $roomId = $move[0]["payload"]["tableId"];
            if ($type === "GAME_OVER") {
                // Chama o GameRoomManager para limpar a sala
                Log::info("ANTES.");
                GameRoomManager::clearRoom($roomId);  // Vamos supor que a função `clearRoom` limpe a sala
                Log::info("Room cleared for tableId: $roomId due to GAME_OVER.");
                Log::info("DEPOIS.");
            }
            Log::info("DEPOIS DO DEPOIS.");
            return response()->json($move);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllMoves($tableId, Request $request)
    {
        try {
            $moves = MatchManager::getAllMoves(
                $tableId
            );
            Log::info($moves);

            // Broadcast para todos na sala via WebSocket
            //broadcast(new \App\Events\RoomUpdated($room));

            return response()->json($moves);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function getLastMove($tableId)
    {
        try {
            $moves = MatchManager::getLastMove(
                $tableId
            );
            Log::info($moves);

            // Broadcast para todos na sala via WebSocket
            //broadcast(new \App\Events\RoomUpdated($room));

            return response()->json($moves);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
