<?php

namespace App\Events\Media;

use App\Models\Present;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Media;

class MediaPresentSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Media
     */
    public $media;

    /**
     * @var Present
     */
    public $present;

    /**
     * User who sent media present
     *
     * @var User
     */
    public $who_sent;
    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @param Present $present
     * @param User $who_sent
     * @return void
     */
    public function __construct(Media $media, Present $present, User $who_sent)
    {
        $this->media = $media;
        $this->present = $present;
        $this->who_sent = $who_sent;
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
