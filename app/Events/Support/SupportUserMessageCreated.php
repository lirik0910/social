<?php

namespace App\Events\Support;

use App\Models\Support;
use App\Models\SupportMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportUserMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $support;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param Support $support
     * @param SupportMessage $support_message
     * @return void
     */
    public function __construct(Support $support, SupportMessage $support_message)
    {
        $this->support = $support;
        $this->data = [
            'support_message' => $support_message,
            'support_request' => $support
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('support_message_receiver.' . $this->support->moderator_id);
    }
}
