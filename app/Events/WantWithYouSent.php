<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WantWithYouSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Type of want with you request
     *
     * @var string
     */
    public $want_with_you_type;

    /**
     * User who received
     *
     * @var User
     */
    public $who_received;

    /**
     * User who sent notification
     *
     * @var User
     */
    public $who_sent;


    /**
     * Create a new event instance.
     *
     * @param integer $want_with_you_type
     * @param User $who_received
     * @return void
     */
    public function __construct(int $want_with_you_type, User $who_received)
    {
        $this->want_with_you_type = $want_with_you_type;
        $this->who_received = $who_received;
        $this->who_sent = Auth::user();
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
