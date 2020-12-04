<?php

namespace App\Events;

use App\Models\Profile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedEvent implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * User profile.
     *
     * @var Profile
     */
    public $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('profile.'.$this->profile->user_id);
    }
}
