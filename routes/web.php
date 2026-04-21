<?php

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $auth = Auth::check();
    /** @var User $user */
    $user = $auth ? Auth::user() : null;
    return Inertia::render('Home', [
        'user' => $user,
        'auth' => $auth,
    ]);
})->name('home');

Route::post('/keluar', function (Request $request) {
    Filament::auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('success', 'You have been logged out.');
})->middleware('auth')->name('logout');