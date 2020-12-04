<?php

namespace App\Notifications\Meeting;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;

class AdvertMeetingCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'meeting.created_for_advert';

    public $meeting;

    /**
     * Create a new notification instance.
     *
     * @param
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->queue = 'notifications';
        $this->connection = 'notifications';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $this->data = $this->getData($notifiable);

        return parent::toArray($notifiable);
    }

    public function toBroadcast($notifiable)
    {
        $this->data = $this->getData($notifiable);

        return parent::toBroadcast($notifiable);
    }

    protected function getData($notifiable = null)
    {
        $auth_user = Auth::user();

        if (!empty($auth_user)) {
            $another_user = $auth_user;
        } else {
            $another_user = $notifiable->id === $this->meeting->seller_id ? $this->meeting->user : $this->meeting->seller;
        }

        return [
            'user' => NotificationsHelper::getNotificationUserData($another_user),
            'info' => [
                'meeting_id' => (string) $this->meeting->id,
                'advert_id' => (string) $this->meeting->inherited->id,
                'advert_type' => $this->meeting->inherited->type,
                'meeting_seller_id' => (string) $this->meeting->seller_id,
                'meeting_user_id' => (string) $this->meeting->user_id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}

