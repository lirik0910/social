<?php

namespace App\Events\Meeting;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\User;
use App\Models\Meeting;

class MeetingRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meeting;

    /**
     * User who request a meeting
     *
     * @var User
     */
    public $requester;

    /**
     * User who received a meeting request
     *
     * @var User
     */
    public $seller;

    /**
     * Create a new event instance.
     *
     * @param Meeting $meeting
     * @param User $requester
     * @param User $seller
     * @return void
     */
    public function __construct(Meeting $meeting, User $requester, User $seller)
    {
        $this->meeting = $meeting;
        $this->requester = $requester;
        $this->seller = $seller;
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
