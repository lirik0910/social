<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendMeetingCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;
    protected $high_priority = true;

    protected function getNotifiable()
    {
        $notifiable = $this->object->seller;

        return $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
    }
}
