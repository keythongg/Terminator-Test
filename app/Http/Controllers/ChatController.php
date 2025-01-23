<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Room;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(Request $request, Room $room)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Kreiraj poruku
        $room->messages()->create([
            'user_id' => auth()->id(),
            'content' => $request->message,
        ]);

        return redirect()->route('rooms.show', $room->id)->with('success', 'Poruka je poslata.');
    }
}
