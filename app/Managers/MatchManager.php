<?php

namespace App\Managers;

use Illuminate\Support\Facades\Cache;
use App\Events\MatchMove;
use Illuminate\Support\Facades\Log;

class MatchManager
{
    private static $cacheKey = 'match';

    public static function initializeMatch($tableId = -1, $force = false)
    {
        $matchCacheKey = self::$cacheKey.$tableId;
        if (!Cache::has($matchCacheKey) || $force) {

            $moves = [];

            Cache::put($matchCacheKey, $moves, now()->addHours(24));
        }

        return Cache::get($matchCacheKey, collect([]));
    }

    public static function getAllMoves($tableId = -1)
    {
        $matchCacheKey = self::$cacheKey.$tableId;

        return collect(Cache::get($matchCacheKey, []));
    }

    public static function getLastMove($tableId = -1)
    {
        $matchCacheKey = self::$cacheKey.$tableId;

        return self::getAllMoves($tableId)->last();
    }

    public static function makeAMove($move)
    {
        $tableId = $move["payload"]["tableId"];

        $matchCacheKey = self::$cacheKey.$tableId;

        // Obtém os movimentos existentes (ou inicia um array vazio)
        $moves = Cache::get($matchCacheKey, []);

        // Adiciona o novo movimento ao array
        $moves[] = [$move];

        // Atualiza o cache com a nova lista de movimentos
        Cache::put($matchCacheKey, $moves);

        broadcast(new MatchMove($move));
        Log::info('MatchMove event triggered.', ['data' => $move]);



        return collect(Cache::get($matchCacheKey, []))->last();
    }

    public static function clearMatch()
    {
        self::initializeMatch(true);
    }
}
