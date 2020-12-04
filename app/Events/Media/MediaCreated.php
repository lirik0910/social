<?php

namespace App\Events\Media;

use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const EVENT_TYPE = 'media.created';

    /**
     * Uploaded media (or first of them)
     *
     * @var Media
     */
    public $media;

    /**
     * Uploaded media count
     *
     * @var integer
     */
    public $count;


    /**
     * Create a new event instance.
     *
     * @param Media $media
     * @param int $count
     * @return void
     */
    public function __construct(Media $media, $count)
    {
        $this->media = $media;
        $this->count = $count;
    }
}
