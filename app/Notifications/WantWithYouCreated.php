<?php

namespace App\Notifications;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\WantWithYou;
use Illuminate\Bus\Queueable;

class WantWithYouCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'want_with_you.created';

    /**
     * User who sent want with you request
     *
     * @var WantWithYou
     */
    protected $want_with_you;

    /**
     * Create a new notification instance.
     *
     * @param WantWithYou $want_with_you
     * @return void
     */
    public function __construct(WantWithYou $want_with_you)
    {
        $this->want_with_you = $want_with_you;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        $who_want = $this->want_with_you->who_want;

        return [
            'user' => NotificationsHelper::getNotificationUserData($who_want),
            'info' => [
                'type' => $this->want_with_you->type,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
