<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendMeetingConfirmationCodeNotification extends AbstractNotificationSending
{
    protected $push = true;
    protected $high_priority = true;

    public function getNotifiable()
    {
        return Collection::wrap($this->object->seller);
    }
}
