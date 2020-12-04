<?php

namespace App\Events\Auction;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Auction;
use App\Models\User;

class AuctionBidNew
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;

    /**
     * Auction bids
     *
     * @var Collection
     */
    public $auction_bids;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Auction $auction
     * @param Collection $auction_bids
     * @param User $user
     * @return void
     */
    public function __construct(Auction $auction, Collection $auction_bids, User $user)
    {
        $this->auction = $auction;
        $this->auction_bids = $auction_bids;
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
