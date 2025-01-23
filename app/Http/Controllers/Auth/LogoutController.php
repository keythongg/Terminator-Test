<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    /**
     * Odjava korisnika i preusmjeravanje na home.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('dashboard')->with('success', 'Uspješno ste se odjavili.');
    }
    
}
