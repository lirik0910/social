<?php


namespace App\Libraries\NotificationsSending;

use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendMediaPresentCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        $notifiable = $this->object->media->user;

        return $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
    }
}
