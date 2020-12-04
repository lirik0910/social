<?php

namespace App\Notifications\Advert;

use App\Helpers\NotificationsHelper;
use App\Models\AdvertRespond;
use Illuminate\Bus\Queueable;
use App\Libraries\GraphQL\AbstractNotification;
use Illuminate\Support\Facades\Auth;

class AdvertRespondCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'advert_respond.created';

    protected $respond;

    /**
     * Create a new notification instance.
     *
     * @param AdvertRespond $respond
     * @return void
     */
    public function __construct(AdvertRespond $respond)
    {
        $this->respond = $respond;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        $user = Auth::user()->id === $this->respond->user_id
            ? Auth::user()
            : $this->respond->user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($user),
            'info' => [
                'advert_id' => (string) $this->respond->advert_id,
                'advert_type' => $this->respond->advert->type
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
