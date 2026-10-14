<?php

use Illuminate\Support\Facades\Broadcast;

// 1. Channel untuk Presence (User Online Real-Time)
Broadcast::channel('online-chat', function ($user) {
    if ($user) {
        // Mengembalikan data user yang sedang online ke channel
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
});

// 2. Channel privat bawaan Laravel (biarkan jika masih dibutuhkan untuk fitur lain)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});