<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::post('/match', [ChatController::class, 'match']);
Route::post('/signal', [ChatController::class, 'signal']);

Route::get('/test-cache', function() {
    $before = \Cache::get('test_key');
    \Cache::put('test_key', 'it_works', 10);
    $after = \Cache::get('test_key');
    return "Before: " . ($before ?: 'null') . " | After: " . ($after ?: 'null');
});
