<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Events\PartnerFound;
use App\Events\SignalSent;

class ChatController extends Controller
{
    public function match(Request $request)
    {
        $userId = $request->input('user_id');
        
        // Cek apakah ada orang yang sedang menunggu di antrean
        $waitingUser = Cache::get('waiting_user');
        
        if ($waitingUser && $waitingUser !== $userId) {
            // Ada orang yang menunggu (dan bukan diri sendiri), kita pasangkan!
            // Hapus dari antrean
            Cache::forget('waiting_user');
            
            // Buat channel ID unik untuk obrolan mereka
            $channelName = 'chat.' . Str::uuid()->toString();
            
            // Beri tahu user yang MENUNGGU bahwa dia sudah dapat pasangan (mengirim role 'caller')
            broadcast(new PartnerFound($waitingUser, $channelName, 'caller', $userId));
            
            // Kembalikan respons ke user yang BARU MASUK (role 'callee')
            return response()->json([
                'status' => 'matched',
                'channel' => $channelName,
                'role' => 'callee',
                'partner_id' => $waitingUser
            ]);
        }
        
        // Jika tidak ada yang menunggu, masukkan user ini ke antrean
        Cache::put('waiting_user', $userId, now()->addMinutes(5));
        
        return response()->json([
            'status' => 'waiting'
        ]);
    }
    
    public function signal(Request $request)
    {
        $channel = $request->input('channel');
        $signalData = $request->input('signal');
        $from = $request->input('from');
        
        // Broadcast signal (SDP/ICE) ke channel chat tersebut
        broadcast(new SignalSent($channel, $signalData, $from));
        
        return response()->json(['status' => 'sent']);
    }
}

