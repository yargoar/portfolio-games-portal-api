<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MatchMove implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $tableId;
    public $move;

    /**
     * Create a new event instance.
     */
    public function __construct($move)
    {
        Log::info("construiu o MatchMove");
        $this->tableId = $move['payload']['tableId'];
        $this->move = $move;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel("match");
    }

    public function broadcastWith()
    {
        return $this->move;
    }

    /**
     * Customize the broadcast name.
     */
    public function broadcastAs(): string
    {
        Log::info("match-{$this->tableId}.updated");
        return "match-{$this->tableId}.updated";
    }
}
