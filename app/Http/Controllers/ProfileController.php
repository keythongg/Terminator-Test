<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('images', 'public');
            
            // Briše staru sliku ako postoji
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
    
            // Ažurira putanju u bazi
            $user->profile_photo_path = $path;
            $user->save();
    
            return response()->json([
                'success' => true,
                'image_path' => asset('storage/' . $path),
            ]);
        }
    
        return response()->json(['success' => false]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateInfo(Request $request)
    {
        // Validacija osnovnih informacija i opcionalne lozinke
        $request->validate([
            'name' => 'required|string|max:255', // Ime je obavezno
            'email' => 'required|email|unique:users,email,' . auth()->id(), // Email mora biti jedinstven
            'broj_mobitela' => 'nullable|string|max:20', // Broj mobitela nije obavezan
            'password' => 'nullable|string|min:8|confirmed', // Lozinka nije obavezna, ali mora biti potvrđena ako je unesena
        ]);
    
        $user = auth()->user();
    
        // Ažuriranje osnovnih informacija
        $user->name = $request->name;
        $user->email = $request->email;
    
        // Ažuriranje broja mobitela ako je unesen
        if ($request->filled('broj_mobitela')) {
            $user->broj_mobitela = $request->broj_mobitela;
        } else {
            $user->broj_mobitela = null; // Očisti broj mobitela ako nije unesen
        }
    
        // Ako je lozinka unesena, ažuriraj je
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'Podaci su uspješno ažurirani.');
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
    
    
    public function showPlayerProfile($id)
    {
        $user = \App\Models\User::findOrFail($id);
    
        // Dohvati sobe u kojima je korisnik bio
        $rooms = $user->rooms()->withPivot('created_at')->get();
    
        // Dohvati ocjene koje je korisnik primio
        $receivedRatings = \App\Models\PlayerRating::where('rated_id', $user->id)->with('rater')->get();
    
        // Dohvati ocjene koje je korisnik dao
        $givenRatings = \App\Models\PlayerRating::where('rater_id', $user->id)->with('rated')->get();
    
        return view('players.profile', compact('user', 'rooms', 'receivedRatings', 'givenRatings'));
    }
    
    
}
