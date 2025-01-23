<?php

namespace App\Http\Controllers;

use App\Models\PlayerRating;
use App\Models\User;
use Illuminate\Http\Request;

class PlayerRatingController extends Controller
{
    // Sprema ocjenu
    public function store(Request $request)
{
    $request->validate([
        'rated_id' => 'required|exists:users,id',
        'room_id' => 'required|exists:rooms,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500',
    ]);

    \DB::table('player_ratings')->insert([
        'rater_id' => auth()->id(),
        'rated_id' => $request->rated_id,
        'room_id' => $request->room_id,
        'rating' => $request->rating,
        'comment' => $request->comment,
        'created_at' => now(),
    ]);

    // Oznaka da je recenzija završena
    \DB::table('room_user')
        ->where('room_id', $request->room_id)
        ->where('user_id', auth()->id())
        ->update(['needs_review' => false]);

    return back()->with('success', 'Recenzija je uspješno dodana!');
}


    // Prikazuje ocjene za određenog igrača
    public function show(User $user)
    {
        $ratings = PlayerRating::where('rated_id', $user->id)->latest()->get();

        return view('ratings.show', compact('user', 'ratings'));
    }



    public function storeMultiple(Request $request)
{
    $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'ratings' => 'required|array',
        'ratings.*.rated_id' => 'required|exists:users,id',
        'ratings.*.rating' => 'required|integer|min:1|max:5',
    ]);

    foreach ($request->ratings as $rating) {
        \DB::table('player_ratings')->insert([
            'rater_id' => auth()->id(),
            'rated_id' => $rating['rated_id'],
            'room_id' => $rating['room_id'],
            'rating' => $rating['rating'],
            'created_at' => now(),
        ]);
    }

    // Oznaka da je recenzija završena za ovog korisnika
    \DB::table('room_user')
        ->where('room_id', $request->room_id)
        ->where('user_id', auth()->id())
        ->update(['needs_review' => false]);

    return back()->with('success', 'Sve recenzije su uspješno poslane!');
}

}
