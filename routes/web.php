<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::post('/match', [\App\Http\Controllers\ChatController::class, 'match']);
Route::post('/signal', [\App\Http\Controllers\ChatController::class, 'signal']);
