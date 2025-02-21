<?php

namespace App\Managers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Events\RoomUpdated;

class GameRoomManager
{
    private static $rooms;
    private static $cacheKey = 'game_rooms';

    public static function initializeRooms($force = false)
    {
        if (!Cache::has(self::$cacheKey) || $force) {

            self::$rooms = collect([
                [
                    'id' => 1,
                    'name' => 'A',
                    'status' => 'AGUARDANDO',
                    'players' => [],
                    'spectators' => []
                ],
                [
                    'id' => 2,
                    'name' => 'B',
                    'status' => 'AGUARDANDO',
                    'players' => [],
                    'spectators' => []
                ],
                [
                    'id' => 3,
                    'name' => 'C',
                    'status' => 'AGUARDANDO',
                    'players' => [],
                    'spectators' => []
                ],
                [
                    'id' => 4,
                    'name' => 'D',
                    'status' => 'AGUARDANDO',
                    'players' => [],
                    'spectators' => []
                ],
            ]);
            Cache::put(self::$cacheKey, self::$rooms, now()->addHours(24));
        }
        self::$rooms = Cache::get(self::$cacheKey, collect([]));
        return self::$rooms;
    }

    public static function getRooms()
    {
        self::$rooms = collect(Cache::get(self::$cacheKey, []));

        return self::$rooms;
    }

    private static function setRooms()
    {
        Cache::put(self::$cacheKey, self::$rooms->toArray());
        return self::$rooms;
    }

    public static function joinRoom($roomId, $userId, $userName = '', $position = 0, $isSpectator = false)
    {
        Log::info("Chega aqui");
        // Recupera as salas do cache ou cria uma Collection vazia
        self::getRooms();

        //remove users from any position it is to join in the selected position
        self::$rooms = self::$rooms->map(function ($room) use ($userId) {
            $room['players'] = array_values(array_filter($room['players'], fn ($player) => $player['id'] != $userId));
            $room['spectators'] = array_values(array_filter($room['spectators'], fn ($spectator) => $spectator['id'] != $userId));
            return $room;
        });
        Log::info("Tira das sala tudo");
        // Procura a sala correta pelo ID para colocar o jogador
        $roomIndex = self::$rooms->search(fn ($r) => $r['id'] === $roomId);

        if ($roomIndex === false) {
            throw new \Exception("Sala não encontrada");
        }

        // Converte o item para array para permitir modificações
        $room = self::$rooms[$roomIndex];

        // Verifica se o usuário já está na sala
        $existingPlayer = collect($room['players'])->contains('id', $userId);
        $existingSpectator = collect($room['spectators'])->contains('id', $userId);
        Log::info("Checa se ele ta na sala, acho q nem precisava");
        if ($existingPlayer) {
            $room['players'] = collect($room['players'])->reject(fn ($p) => $p['id'] === $userId)->toArray();
        }

        if ($existingSpectator) {
            $room['spectators'] = collect($room['spectators'])->reject(fn ($s) => $s['id'] === $userId)->toArray();
        }

        if ($isSpectator) {
            $room['spectators'][] = ['id' => $userId, 'name' => $userName];
        } else {
            if (count($room['players']) < 2) {
                Log::info("bota na sala");
                $room['players'][] = ['id' => $userId, 'name' => $userName, 'position' => $position];

                // Atualiza o status SE atingir 2 jogadores
                if (count($room['players']) === 2) {
                    $room['status'] = 'JOGANDO';
                    $room['status'] = 'JOGANDO';
                }
            } else {
                throw new \Exception("Sala cheia");
            }
        }

        // Atualiza a Collection corretamente
        self::$rooms[$roomIndex] = $room;

        // Atualiza o cache
        Log::info('RoomUpdated event triggered.', ['data' => $room]);
        self::setRooms();
        broadcast(new RoomUpdated($room));
        return $room;
    }


    public static function leaveRoom($roomId, $userId)
    {
        // Recupera as salas do cache ou cria uma Collection vazia
        $rooms = self::getRooms();

        // Procura a sala correta pelo ID
        $roomIndex = $rooms->search(fn ($r) => $r['id'] === $roomId);

        if ($roomIndex === false) {
            throw new \Exception("Sala não encontrada");
        }

        // Obtém a sala como array para modificar
        $room = $rooms[$roomIndex];

        // Remove o usuário da lista de players e spectators
        $room['players'] = collect($room['players'])->reject(fn ($p) => $p['id'] === $userId)->toArray();
        $room['spectators'] = collect($room['spectators'])->reject(fn ($s) => $s['id'] === $userId)->toArray();

        // Atualiza o status da sala se necessário
        if (count($room['players']) < 2) {
            $room['status'] = 'AGUARDANDO';
        }

        // Atualiza a Collection corretamente
        $rooms[$roomIndex] = $room;

        // Atualiza o cache
        self::setRooms($rooms);
        broadcast(new RoomUpdated($room));
        return $room;
    }

    public static function clearRoom($roomId)
    {
        // Recupera as salas do cache ou cria uma Collection vazia
        $rooms = self::getRooms();

        // Procura a sala correta pelo ID
        $roomIndex = $rooms->search(fn ($r) => $r['id'] === $roomId);

        if ($roomIndex === false) {
            throw new \Exception("Sala não encontrada");
        }

        // Obtém a sala como array para modificar
        $room = $rooms[$roomIndex];

        $room['players'] = [];
        $room['spectators'] = [];
        $room['status'] = 'AGUARDANDO';

        $rooms[$roomIndex] = $room;

        self::setRooms($rooms);
        broadcast(new RoomUpdated($room));
        return $room;
    }



    public static function clearAllRooms()
    {
        self::initializeRooms(true);
    }
}
