<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendMediaBannedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        $notifiable = $this->object->user;

        return $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
    }
}
