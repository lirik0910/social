<?php

namespace App\Notifications;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SubscribeCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'subscribe.created';

    /**
     * User who became a new subscriber
     *
     * @var User
     */
    protected $subscribe_record;

    /**
     * Create a new notification instance.
     *
     * @param Subscribe $subscribe_record
     * @return void
     */
    public function __construct(Subscribe $subscribe_record)
    {
        $this->subscribe_record = $subscribe_record;
        $this->data = $this->getData();
    }


    protected function getData($notifiable = null)
    {
        $subscriber = $this->subscribe_record->subscriber_user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($subscriber),
            'type' => self::EVENT_TYPE,
        ];
    }
}
