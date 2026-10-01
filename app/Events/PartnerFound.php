<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PartnerFound implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $targetUserId;
    public $channelName;
    public $role;
    public $partnerId;

    public function __construct($targetUserId, $channelName, $role, $partnerId)
    {
        $this->targetUserId = $targetUserId;
        $this->channelName = $channelName;
        $this->role = $role;
        $this->partnerId = $partnerId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->targetUserId),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'PartnerFound';
    }
}

