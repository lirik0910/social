<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;

class SendAdvertUpdatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        return $this->object
            ->responds()
            ->where('users.id', '!=', $this->object->respond_user_id)
            ->get()
            ->filter(function ($user, $key) {
                return $user->isNotifiable($this->path);
            });
    }
}
