<?php

namespace App\Notifications\Meeting;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;

class MeetingCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'meeting.created';

    public $data;

    /**
     * Received meeting
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     * Create a new notification instance.
     *
     * @param Meeting $meeting
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        $user_who_request = $this->meeting->user_id === Auth::user()->id
            ? Auth::user()
            : $this->meeting->user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($user_who_request),
            'info' => [
                'meeting_id' => (string) $this->meeting->id,
                'meeting_price' => (int) $this->meeting->price,
                'meeting_seller_id' => (string) $this->meeting->seller_id,
                'meeting_user_id' => (string) $this->meeting->user_id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
