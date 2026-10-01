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
        $waitingUser = Cache::get('waiting_user');
        if ($waitingUser && $waitingUser !== $userId) {
            Cache::forget('waiting_user');
            $channelName = 'chat.' . Str::uuid()->toString();
            broadcast(new PartnerFound($waitingUser, $channelName, 'caller', $userId));
            return response()->json([
                'status' => 'matched',
                'channel' => $channelName,
                'role' => 'callee',
                'partner_id' => $waitingUser
            ]);
        }
        
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
        broadcast(new SignalSent($channel, $signalData, $from));
        
        return response()->json(['status' => 'sent']);
    }
}

