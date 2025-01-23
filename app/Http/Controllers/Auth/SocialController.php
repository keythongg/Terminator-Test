<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Http\Request; 


class SocialController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
    
            // Pronađi korisnika po emailu ili facebook_id ili ga kreiraj
            $user = User::updateOrCreate(
                ['email' => $facebookUser->email], // Pretraga korisnika po emailu
                [
                    'name' => $facebookUser->name,
                    'facebook_id' => $facebookUser->id, // Dodaj facebook_id
                    'password' => bcrypt('random-password'), // Generiši nasumičnu lozinku
                ]
            );
    
            // Uloguj korisnika
            Auth::login($user);
    
            return redirect('/rooms');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Došlo je do greške: ' . $e->getMessage());
        }
    }
    

    // Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            // Pronađi korisnika po emailu ili ga kreiraj
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                ]
            );
    
            // Provjeri da li korisnik ima šifru (password je null)
            if (is_null($user->password)) {
                // Postavi user_id u sesiju za kasniju upotrebu
                session(['user_id' => $user->id]);
    
                Auth::login($user);
                return redirect()->route('password.create')->with('user_id', $user->id); // Preusmjeri na kreiranje šifre
            }
    
            // Ako korisnik već ima šifru, samo ga uloguj
            Auth::login($user);
    
            return redirect('/rooms');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Došlo je do greške: ' . $e->getMessage());
        }
    }
    

    

    public function createPasswordForm(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/login')->with('error', 'Pristup nije dozvoljen.');
        }
        return view('auth.create-password', ['user_id' => $userId]);
    }
    
    public function storePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::findOrFail($request->user_id);
        $user->password = bcrypt($request->password);
        $user->save();
    
        Auth::login($user);
    
        return redirect('/rooms')->with('success', 'Šifra uspješno kreirana.');
    }
    

}
