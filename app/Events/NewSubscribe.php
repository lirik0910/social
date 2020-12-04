<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewSubscribe
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $subscriber;

    /**
     * @var User
     */
    public $subscribe_user;

    /**
     * Create a new event instance.
     *
     * @param User $subscriber
     * @param User $subscribe_user
     * @return void
     */
    public function __construct(User $subscriber, User $subscribe_user)
    {
        $this->subscriber = $subscriber;
        $this->subscribe_user = $subscribe_user;
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
