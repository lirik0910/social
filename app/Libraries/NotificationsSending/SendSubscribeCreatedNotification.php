<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendSubscribeCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        return $this->object->owner_user->isNotifiable($this->path) && is_null($this->object->deleted_at) ? Collection::wrap($this->object->owner_user) : collect([]);
    }
}
