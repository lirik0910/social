<?php

namespace App\Events\Support;

use App\Models\Support;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $support;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param Support $support
     * @param array $data
     * @return void
     */
    public function __construct($support, $data)
    {
        $this->support = $support;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('changed_support.' . $this->support->id);
    }
}
