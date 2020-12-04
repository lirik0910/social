<?php

namespace App\Notifications\Meeting;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingConfirmationCode extends AbstractNotification implements ShouldQueue
{
    use Queueable;

    const EVENT_TYPE = 'meeting.confirmation_code';

    public $meeting;

    /**
     * Create a new notification instance.
     *
     * @param Meeting $meeting
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
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
        $another_user = $notifiable->id === $this->meeting->seller_id ? $this->meeting->user : $this->meeting->seller;

        return [
            'user' => NotificationsHelper::getNotificationUserData($another_user),
            'info' => [
                'meeting_id' => (string) $this->meeting->id,
                'meeting_code' => decrypt($this->meeting->confirmation_code),
                'meeting_seller_id' => (string) $this->meeting->seller_id,
                'meeting_user_id' => (string) $this->meeting->user_id
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
