<?php

namespace App\Notifications\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MediaVerification extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'media.verification';

    /**
     * @var Media
     */
    protected $media;

    /**
     * Create a new notification instance.
     *
     * @param Media $media
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $thumbnail_size = MediaHelper::getThumbSizes($this->media)[1];

        return [
            'info' => [
                'media_id' => (string) $this->media->id,
                'media_status' => (int) $this->media->status,
                'media_thumbnail' => $this->media->getThumbs($this->media, ["sizes" => [$thumbnail_size]]),
                'media_type' => $this->media->type,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
