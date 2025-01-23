<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use App\Notifications\PlayerInviteNotification;
use App\Models\City; 

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all(); // Pretpostavka da učitavate sve sobe iz baze
        $locations = Location::all();
        $cities = City::all();
        $notifications = auth()->check()
            ? auth()->user()->unreadNotifications
            : collect(); // Prazna kolekcija ako korisnik nije prijavljen
    
        // Provjeri treba li prikazati modal za recenzije
        $needsReview = auth()->check()
            ? auth()->user()->rooms()->wherePivot('needs_review', true)->with('players')->get()
            : collect(); // Prazna kolekcija ako korisnik nije prijavljen
    
        return view('rooms.index', compact('rooms', 'locations', 'cities', 'notifications', 'needsReview'));
    }
    
    

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();  // Dohvati sve korisnike
        $locations = Location::all(); // Lokacije ako su potrebne
        $cities = City::all();  // Učitavanje gradova
    
        return view('rooms.create', compact('users', 'locations', 'cities'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',  // Dozvoli NULL za lokaciju
            'city_id' => 'required|exists:cities,id',
            'date' => 'required|date',  // Validacija za datum
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'max_players' => 'required|integer|min:1',
            'password' => 'nullable|string|min:4',
        ]);
    
        Room::create([
            'title' => $request->title,
            'description' => $request->description,
            'location_id' => $request->location_id,
            'city_id' => $request->city_id,
            'date' => $request->date,  // Osiguraj da date ide u bazu
            'start_time' => $request->start_time ?? '00:00:00',
            'end_time' => $request->end_time ?? '00:00:00',
            'max_players' => $request->max_players,
            'owner_id' => auth()->id(),
            'password' => $request->filled('password') ? bcrypt($request->password) : null,
        ]);
    
        return redirect()->route('rooms.index')->with('success', 'Termin je uspješno kreiran.');
    }
    
    
    

    /**
     * Display the specified resource.
     */


     public function show(Room $room)
     {
         $players = $room->players;  // Igrači u sobi
         $messages = $room->messages()->with('user')->latest()->get();
         
         // Dohvati sve korisnike osim onih koji su već u sobi
         $users = User::whereNotIn('id', $players->pluck('id'))->get();
     
         return view('rooms.show', [
             'room' => $room,
             'players' => $players,
             'messages' => $messages,
             'users' => $users,  // Ovdje prosljeđujemo users varijablu
         ]);
     }
     

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        if (auth()->id() !== $room->owner_id) {
            return redirect()->route('rooms.index')->with('error', 'Nemate ovlašćenje za uređivanje ovog termina.');
        }
    
        $locations = Location::where('city_id', $room->city_id)->get(); // Filtriraj lokacije po gradu
        $cities = City::all();  // Učitaj sve gradove
        
        return view('rooms.edit', compact('room', 'locations', 'cities'));
    }
    
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        if (auth()->id() !== $room->owner_id) {
            return redirect()->route('rooms.index')->with('error', 'Nemate ovlašćenje za izmjenu ovog termina.');
        }
    
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'required|exists:locations,id',
            'city_id' => 'required|exists:cities,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_players' => 'required|integer|min:1',
        ]);
    
        $room->update($request->all());
    
        return redirect()->route('rooms.index')->with('success', 'Termin uspješno ažuriran.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Provera da li trenutni korisnik ima pravo da obriše termin
        if (auth()->id() !== $room->owner_id) {
            return redirect()->route('rooms.index')->with('error', 'Nemate ovlašćenje za brisanje ovog termina.');
        }
    
        // Soft delete termina
        $room->delete();
    
        return redirect()->route('rooms.index')->with('success', 'Termin uspješno obrisan.');
    }
    

    public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}

public function join(Request $request, Room $room)
{
    if ($room->players()->where('user_id', auth()->id())->exists()) {
        return redirect()->back()->with('error', 'Već ste pridruženi ovom terminu.');
    }

    $room->players()->attach(auth()->id());

    return redirect()->route('rooms.show', $room->id)->with('success', 'Uspješno ste se pridružili terminu.');
}





public function checkPassword(Request $request, Room $room)
{
    // Provjeri lozinku
    if (Hash::check($request->password, $room->password)) {
        // Pridruži korisnika sobi ako već nije pridružen
        if (!$room->players()->where('user_id', auth()->id())->exists()) {
            $room->players()->attach(auth()->id());
        }

        // Ako je lozinka tačna
        return response()->json(['success' => true]);
    }

    // Ako lozinka nije ispravna
    return response()->json(['success' => false, 'message' => 'Pogrešna lozinka.']);
}


public function enter(Room $room)
{
    // Proveri da li je korisnik član sobe
    if (!$room->players()->where('user_id', auth()->id())->exists()) {
        return redirect()->route('rooms.index')->with('error', 'Morate se prvo pridružiti terminu.');
    }

    // Prikaz stranice sobe
    $players = $room->players;
    $messages = $room->messages()->with('user')->latest()->get();

    return view('rooms.show', [
        'room' => $room,
        'players' => $players,
        'messages' => $messages,
    ]);
}



public function leave(Room $room)
{
    // Proveri da li je korisnik član sobe
    if (!$room->players()->where('user_id', auth()->id())->exists()) {
        return redirect()->route('rooms.index')->with('error', 'Niste član ove sobe.');
    }

    // Ukloni korisnika iz sobe
    $room->players()->detach(auth()->id());

    return redirect()->route('rooms.index')->with('success', 'Uspješno ste napustili sobu.');
}

public function checkJoinStatus(Room $room)
{
    $isJoined = $room->players()->where('user_id', auth()->id())->exists();

    return response()->json(['joined' => $isJoined]);
}

public function kickPlayer(Room $room, $playerId)
{
    // Provjeri da li je trenutni korisnik vlasnik sobe
    if (auth()->id() !== $room->owner_id) {
        return response()->json(['error' => 'Nemate ovlasti za ovu akciju.'], 403);
    }

    // Provjeri da li igrač pripada sobi
    if (!$room->players()->where('user_id', $playerId)->exists()) {
        return response()->json(['error' => 'Igrač nije član ove sobe.'], 404);
    }

    // Ukloni igrača iz sobe
    $room->players()->detach($playerId);

    return response()->json(['success' => true, 'message' => 'Igrač je uspješno izbačen.']);
}


public function zavrsavanjeTermina(Room $room)
{
    // Provera da li trenutni korisnik ima pravo da obriše termin
    if (auth()->id() !== $room->owner_id) {
        return redirect()->route('rooms.index')->with('error', 'Nemate ovlašćenje da završite ovaj termin.');
    }

    // Soft delete termina
    $room->delete();

    return redirect()->route('rooms.index')->with('success', 'Termin uspješno završen.');
}



public function finish($id)
{
    $room = Room::findOrFail($id);
    
    \Log::info('Pokušaj završetka termina: ' . $room->id);

    if (auth()->id() !== $room->owner_id) {
        \Log::warning('Neuspješan pokušaj završetka - Nije vlasnik');
        return redirect()->back()->with('error', 'Nemate ovlaštenje za završavanje ovog termina.');
    }
    
    $room->status = 'zavrsen';
    $room->save();

    \Log::info('Termin uspješno završen: ' . $room->id);

    // Poziv destroy metode
    return $this->zavrsavanjeTermina($room);
}

public function invitePlayer(Request $request)
{
    $room = Room::findOrFail($request->room_id);
    $user = User::findOrFail($request->user_id);
    $inviter = auth()->user();  // Trenutni korisnik

    // Dodaj notifikaciju korisniku
    $notification = $user->notify(new PlayerInviteNotification($room, auth()->user()));

    return redirect()->back()->with('success', 'Igrač je uspješno pozvan.');
}


public function searchPlayers(Request $request)
{
    $query = $request->input('query');
    $roomId = $request->input('room_id');

    $players = User::where('name', 'LIKE', "%{$query}%")
                   ->whereNotIn('id', function($q) use ($roomId) {
                       $q->select('user_id')->from('room_user')->where('room_id', $roomId);
                   })
                   ->get();

    return response()->json($players);
}


public function notifications()
{
    // Učitavamo sve notifikacije za trenutno prijavljenog korisnika
    $notifications = auth()->user()->notifications;

    return view('notifications.index', compact('notifications'));
}


public function getLocationsByCity(Request $request)
{
    $locations = Location::where('city_id', $request->city_id)->get();
    return response()->json($locations);
}


public function ratePlayer(Request $request)
{
    $request->validate([
        'rater_id' => 'required|exists:users,id',
        'rated_id' => 'required|exists:users,id|different:rater_id',
        'room_id' => 'required|exists:rooms,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:500',
    ]);

    \DB::table('player_ratings')->insert([
        'rater_id' => $request->rater_id,
        'rated_id' => $request->rated_id,
        'room_id' => $request->room_id,
        'rating' => $request->rating,
        'comment' => $request->comment,
        'created_at' => now(),
    ]);

    return back()->with('success', 'Ocjena uspješno dodana!');
}


public function finishWithReviews(Request $request, $id)
{
    // Dohvaćanje sobe
    $room = Room::findOrFail($id);

    \Log::info('Pokušaj završetka termina: ' . $room->id);

    // Provjera ovlaštenja
    if (auth()->id() !== $room->owner_id) {
        \Log::warning('Neuspješan pokušaj završetka - Nije vlasnik');
        return redirect()->back()->with('error', 'Nemate ovlaštenje za završavanje ovog termina.');
    }

    // Prisilno ažuriranje statusa
    $room->status = 'zavrsen';
    $room->save();

    \Log::info('Status ažuriran na "zavrsen" za sobu: ' . $room->id);

    // Postavljanje needs_review za sve igrače
    $room->players()->update(['needs_review' => true]);

    // Oznaka za vlasnika sobe da treba ostaviti recenzije
    \DB::table('room_user')
        ->where('room_id', $room->id)
        ->where('user_id', $room->owner_id)
        ->update(['needs_review' => true]);

    \Log::info('Svi učesnici, uključujući vlasnika, označeni su za recenzije.');

    // Dodavanje podataka u sesiju za prikaz modala
    session()->flash('show_review_modal', true);
    session()->flash('room_id', $room->id);

    \Log::info('Sesija postavljena za prikaz modala.');

    // Vrati response prije brisanja sobe
    return redirect()->route('rooms.index')->with('success', 'Termin je uspješno završen. Svi učesnici će dobiti modal za recenzije.');
}


}
