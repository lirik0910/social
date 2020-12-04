<?php

namespace App\Notifications;

use App\Libraries\GraphQL\AbstractNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;


class ReportsCountLimitReached extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'user.reports_count_limit_reached';

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        return [
            'type' => self::EVENT_TYPE,
        ];
    }
}
