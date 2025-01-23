<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Provjeri da li koristiš User model
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count(); // Broji korisnike
        $activeRooms = Room::where('status', 'zakazan')->count(); // Aktivni termini
        $completedRooms = Room::where('status', 'zavrsen')->count(); // Završeni termini
        $recentUsers = User::latest()->take(5)->get(); // Zadnji korisnici

        // Prosljeđivanje u view
        return view('dashboard', compact('userCount', 'activeRooms', 'completedRooms', 'recentUsers'));
    }
}
