<?php

namespace App\Events\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvatarVerifyingReject
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Avatar owner
     *
     * @var User|\Illuminate\Foundation\Auth\User
     */
    public $user;

    /**
     * Verifying avatar
     *
     * @var Media
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @param User|\Illuminate\Foundation\Auth\User $user
     * @param Media $media
     * @return void
     */
    public function __construct($user, Media $media)
    {
        $this->user = $user;
        $this->media = $media;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //TODO Rename channel
        return new PrivateChannel('channel-name');
    }
}
