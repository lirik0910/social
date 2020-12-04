<?php

namespace App\Events\Auction;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class AuctionCanceled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Auction $auction
     * @return void
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
        $this->user = Auth::user();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
