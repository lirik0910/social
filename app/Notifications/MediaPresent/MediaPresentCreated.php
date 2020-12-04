<?php

namespace App\Notifications\MediaPresent;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\MediaPresent;
use Illuminate\Bus\Queueable;


class MediaPresentCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'media_present.created';

    protected $media_present;

    /**
     * Create a new notification instance.
     *
     * @param $media_present
     * @return void
     */
    public function __construct(MediaPresent $media_present)
    {
        $this->media_present = $media_present;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     */
    protected function getData($notifiable = null)
    {
        $present_user = $this->media_present->user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($present_user),
            'info' => [
                'media_id' => (string) $this->media_present->media_id,
                'media_type' => $this->media_present->media->type
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
