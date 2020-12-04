<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;
    public $data;

    public function __construct($auction, $new_bid)
    {
        $this->auction = $auction;
        $this->data = [
            'bid' => $new_bid->value,
            'user' => $new_bid->user,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('updated_ auction.' . $this->auction->id);
    }
}
