<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-cache', function() {
    $before = \Cache::get('test_key');
    \Cache::put('test_key', 'it_works', 10);
    $after = \Cache::get('test_key');
    return "Before: " . ($before ?: 'null') . " | After: " . ($after ?: 'null');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

// Route untuk login Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Rute untuk menampilkan dan menyimpan setup profil (Wajib login)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/setup', [ProfileController::class, 'showProfileForm'])->name('profile.setup');
    Route::post('/profile/setup', [ProfileController::class, 'updateProfile'])->name('profile.update');
});

// Proteksi halaman chat (Harus login DAN sudah melengkapi profil)
Route::middleware(['auth', 'profile.completed'])->group(function () {
    Route::get('/chat', function () {
        return view('chat');
    });
    
    Route::post('/match', [ChatController::class, 'match']);
    Route::post('/signal', [ChatController::class, 'signal']);
});