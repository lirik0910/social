<?php

namespace App\Events\User;

use App\Models\Media;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvatarChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * Avatar owner
     *
     * @var User|\Illuminate\Foundation\Auth\User
     */
    public $user;

    /**
     * Uploaded avatar
     *
     * @var Media
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @param User|\Illuminate\Foundation\Auth\User $user
     * @param Media|null $media
     * @return void
     */
    public function __construct($user, $media)
    {
        $this->user = $user;
        $this->media = $media;
    }
}
