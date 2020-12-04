<?php

namespace App\Notifications\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MediaCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'media.created';

    /**
     * Created Media
     *
     * @var Media
     */
    protected $media;

    /**
     * Created media count
     *
     * @var int
     */
    protected $count;

    /**
     * Create a new notification instance.
     *
     * @param $media
     * @param int $count
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function __construct(Media $media, int $count)
    {
        $this->media = $media;
        $this->count = $count;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $user = $this->media->user;
        $thumbnail_size = MediaHelper::getThumbSizes($this->media)[1];

        return [
            'user' => NotificationsHelper::getNotificationUserData($user),
            'info' => [
                'media_id' => (string) $this->media->id,
                'media_thumbnail' => $this->media->getThumbs($this->media, ["sizes" => [$thumbnail_size]]),
                'media_type' => $this->media->type,
                'count' => $this->count
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
