<?php

namespace App\Notifications\Meeting;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Auction;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AuctionMeetingCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'meeting.created_for_auction';

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
        $another_user = $notifiable->id === $this->meeting->seller_id ? $this->meeting->user : $this->meeting->seller;

        return [
            'user' => NotificationsHelper::getNotificationUserData($another_user),
            'info' => [
                'meeting_id' => (string) $this->meeting->id,
                'auction_id' => (string) $this->meeting->inherited_id,
                'meeting_seller_id' => (string) $this->meeting->seller_id,
                'meeting_user_id' => (string) $this->meeting->user_id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
