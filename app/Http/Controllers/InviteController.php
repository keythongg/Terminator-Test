<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Room;




class InviteController extends Controller
{
    public function accept(Notification $notification)
    {
        // Markiraj notifikaciju kao pročitanu
        $notification->markAsRead();
    
        // Pronađi sobu iz notifikacije
        $roomId = $notification->data['room_id'];
    
        // Dodaj korisnika u sobu
        $room = Room::findOrFail($roomId);
    
        // Proveri da li je korisnik već član sobe
        if (!$room->players()->where('user_id', auth()->id())->exists()) {
            $room->players()->attach(auth()->id());
        }
    
        return redirect()->route('rooms.enter', $roomId)->with('success', 'Uspješno ste se pridružili terminu.');
    }
    

    public function decline(Notification $notification)
{
    $notification->delete();
    return redirect()->back()->with('info', 'Pozivnica odbijena.');
}

}
