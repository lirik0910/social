<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendWantWithYouCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        return $this->object->receiver->isNotifiable($this->path) ? Collection::wrap($this->object->receiver) : collect([]);
    }
}
