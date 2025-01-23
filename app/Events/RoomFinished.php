<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomFinished
{
    use Dispatchable, SerializesModels;

    public $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function broadcastOn()
    {
        return new Channel('rooms');  // Emitovanje na kanal 'rooms'
    }

    public function broadcastWith()
    {
        return ['room_id' => $this->room->id];  // Šalje ID sobe koja je završena
    }
}
