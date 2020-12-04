<?php

namespace App\Events\Meeting;

use App\Models\Auction;
use App\Models\Meeting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AuctionMeetingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;
    public $meeting;

    /**
     * Create a new event instance.
     *
     * @param Auction $auction
     * @param Meeting $meeting
     * @return void
     */
    public function __construct(Auction $auction, Meeting $meeting)
    {
        $this->auction = $auction;
        $this->meeting = $meeting;
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
