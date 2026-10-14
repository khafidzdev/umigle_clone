<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use App\Events\PartnerFound;
use App\Events\SignalSent;

class ChatController extends Controller
{
    public function match(Request $request)
    {
        // Ambil data user yang sedang aktif saat ini beserta negaranya
        $currentUser = auth()->user();
        $userId = $request->input('user_id');
        
        // Kita simpan array yang berisi ID dan Country user di cache antrean
        $waitingUserData = Cache::get('waiting_user'); 

        if ($waitingUserData && $waitingUserData['user_id'] !== $userId) {
            // Hapus antrean karena sudah ada pasangan
            Cache::forget('waiting_user');
            
            $channelName = 'chat.' . Str::uuid()->toString();
            
            // 1. Beritahu user yang sedang menunggu (caller)
            // Kirim juga negara user saat ini ke partner yang menunggu
            broadcast(new PartnerFound($waitingUserData['user_id'], $channelName, 'caller', $userId, $currentUser->country));
            
            // 2. Beri respons JSON ke user saat ini (callee) beserta negara partner yang menunggu
            return response()->json([
                'status' => 'matched',
                'channel' => $channelName,
                'role' => 'callee',
                'partner_id' => $waitingUserData['user_id'],
                'country' => $waitingUserData['country'] // Negara lawan bicara (yang menunggu)
            ]);
        }
        
        // Jika belum ada partner, masukkan user saat ini ke dalam antrean cache beserta negaranya
        Cache::put('waiting_user', [
            'user_id' => $userId,
            'country' => $currentUser->country
        ], now()->addMinutes(5)); 
        
        return response()->json([
            'status' => 'waiting'
        ]);
    }
    
    public function signal(Request $request)
    {
        $channel = $request->input('channel');
        $signalData = $request->input('signal');
        $from = $request->input('from');
        
        broadcast(new SignalSent($channel, $signalData, $from));
        
        return response()->json(['status' => 'sent']);
    }
}