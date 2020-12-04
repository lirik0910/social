<?php

namespace App\Events\Auction;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Auction;

class AuctionStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;

    /**
     * User who start auction
     *
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Auction $auction
     * @param User $user
     * @return void
     */
    public function __construct(Auction $auction, User $user)
    {
        $this->auction = $auction;
        $this->user = $user;
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
