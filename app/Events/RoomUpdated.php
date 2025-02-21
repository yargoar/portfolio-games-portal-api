<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Log;

class RoomUpdated implements ShouldBroadcastNow
{
    public $room;

    public function __construct($room)
    {
        $this->room = $room;
    }

    public function broadcastOn()
    {
        return new Channel('rooms');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->room['id'],
            'name' => $this->room['name'],
            'status' => $this->room['status'],
            'players' => $this->room['players'],
            'spectators' => $this->room['spectators'],
        ];
    }

    // Opcional: Nome do evento (padrão é o nome da classe)
    public function broadcastAs()
    {
        return 'room.updated';
    }
}
