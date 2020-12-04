<?php

namespace App\Events;

use App\Models\PhotoVerification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PhotoVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PhotoVerification
     */
    public $verified_photo;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param PhotoVerification $verified_photo
     * @param User $user
     * @return void
     */
    public function __construct(PhotoVerification $verified_photo, User $user)
    {
        $this->verified_photo = $verified_photo;
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
