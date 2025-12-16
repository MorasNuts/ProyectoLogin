<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



Route::get('/google-auth/redirect', action: function (): RedirectResponse {
    return Socialite::driver('google')->redirect();
});

Route::get('/google-auth/callback', action: function (): RedirectResponse {
    // 1. Obtener los detalles del usuario de Google
    $user_google = Socialite::driver('google')->user();
    $user = User::updateOrCreate(
        attributes: ['google_id' => $user_google->id],
        values: [
            'name' => $user_google->name,
            'email' => $user_google->email,
        ]
    );
    Auth::login(user: $user);
    return redirect(to: '/dashboard');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
