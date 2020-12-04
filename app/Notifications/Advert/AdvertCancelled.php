<?php

namespace App\Notifications\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Advert;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;


class AdvertCancelled extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'advert.cancelled';

    protected $advert;

    /**
     * Create a new notification instance.
     *
     * @param Advert $advert
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $user_info = Auth::user() && Auth::user()->role !== User::ROLE_USER
            ? null
            : NotificationsHelper::getNotificationUserData($this->advert->user);

        return [
            'user' => $user_info,
            'info' => [
                'advert_id' => (string) $this->advert->id,
                'advert_price' => $this->advert->price,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
