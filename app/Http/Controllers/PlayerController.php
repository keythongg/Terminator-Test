<?php

namespace App\Http\Controllers;

use App\Models\User; // Pretpostavljamo da koristite model User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function profile($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return redirect()->route('rooms.index')->with('error', 'Igrač nije pronađen.');
        }
    
        // Dohvati sobe u kojima je korisnik učestvovao
        $rooms = $user->rooms()->withPivot('created_at')->get();
    
        return view('players.profile', compact('user', 'rooms'));
    }
    
   
    public function show()
{
    $user = auth()->user();

    // Dohvati ocjene koje je korisnik primio
    $receivedRatings = \App\Models\PlayerRating::where('rated_id', $user->id)
        ->with('rater') // Preloader za podatke o korisniku koji je dao ocjenu
        ->latest()
        ->get();

    // Dohvati ocjene koje je korisnik dao
    $givenRatings = \App\Models\PlayerRating::where('rater_id', $user->id)
        ->with('rated') // Preloader za podatke o korisniku koji je primio ocjenu
        ->latest()
        ->get();

    return view('profile', compact('user', 'receivedRatings', 'givenRatings'));
}

  
}
