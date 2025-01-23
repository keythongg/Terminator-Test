<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PlayerRatingController;


// Početna stranica (home/dashboard)
Route::get('/', function () {
    return view('welcome'); // Naziv fajla u 'resources/views'
})->name('home');


// Dashboard ruta (za prijavljene korisnike)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
});

// Profile rute (zaštićeno)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rooms rute (zaštićene)
Route::middleware('auth')->group(function () {
    Route::resource('rooms', RoomController::class)->except(['index']);
    Route::post('/rooms/{room}/check-password', [RoomController::class, 'checkPassword'])->name('rooms.checkPassword');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');
    Route::get('/rooms/{room}/enter', [RoomController::class, 'enter'])->name('rooms.enter');
    Route::post('/rooms/{room}/leave', [RoomController::class, 'leave'])->name('rooms.leave');
    Route::post('/rooms/{room}/kick/{player}', [RoomController::class, 'kickPlayer'])->name('rooms.kick');
});

// Chat ruta
Route::post('/rooms/{room}/chat/send', [ChatController::class, 'send'])->name('chat.send');

// Player ruta
Route::get('/players/{id}/profile', [PlayerController::class, 'profile'])->name('players.profile');

// Facebook i Instagram autentifikacija
Route::get('auth/facebook', [SocialController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

Route::get('auth/instagram', [SocialController::class, 'redirectToInstagram']);
Route::get('auth/instagram/callback', [SocialController::class, 'handleInstagramCallback']);

// Login i registracija
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// Logout sa redirekcijom na home
Route::post('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');


// Ruta za javno prikazivanje svih termina
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

// Ruta za role
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});


Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/profile/update/info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');

Route::get('/locations/by-city', [RoomController::class, 'getLocationsByCity']);


Route::post('/rooms/{id}/finish', [RoomController::class, 'finish'])->name('rooms.finish');

Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');

Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');


Route::get('/search-players', [RoomController::class, 'searchPlayers']);
Route::post('/invite-player', [RoomController::class, 'invitePlayer'])->name('invite.player');


Route::get('/notifications', [RoomController::class, 'notifications'])->name('notifications');

Route::get('/notifications/unread', function () {
    return auth()->user()->unreadNotifications;
})->middleware('auth');


Route::delete('/invite/{notification}/decline', [InviteController::class, 'decline'])->name('invite.decline');

Route::post('/invite/{notification}/accept', [InviteController::class, 'accept'])->name('invite.accept');


Route::post('/invite/{notification}/decline', [InviteController::class, 'decline'])->name('invite.decline');


Route::middleware(['auth'])->group(function () {
    Route::post('/ratings', [PlayerRatingController::class, 'store'])->name('ratings.store');
    Route::get('/ratings/{user}', [PlayerRatingController::class, 'show'])->name('ratings.show');
});

Route::post('/rooms/{room}/finish-with-reviews', [RoomController::class, 'finishWithReviews'])->name('rooms.finishWithReviews');

Route::post('/ratings/store-multiple', [PlayerRatingController::class, 'storeMultiple'])->name('ratings.storeMultiple');


Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/players/{id}/profile', [ProfileController::class, 'showPlayerProfile'])->name('players.profile');

Route::get('auth/facebook', [SocialController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

Route::get('auth/google', [SocialController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');

Route::get('/password/create', [SocialController::class, 'createPasswordForm'])->name('password.create');
Route::post('/password/store', [SocialController::class, 'storePassword'])->name('password.store');



// Auth rute (defaultne Laravel auth rute)
require __DIR__ . '/auth.php';
