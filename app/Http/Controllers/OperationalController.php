<?php

namespace App\Http\Controllers;

use App\Managers\GameRoomManager;
use Illuminate\Http\Request;

class OperationalController extends Controller
{
    public function clearRooms(Request $request)
    {
        GameRoomManager::clearAllRooms();
        return 'Rooms deleted';
    }
}
