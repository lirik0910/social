<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Support\Facades\Auth;

class SendAdvertCancelledNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        $notifiable = $this->object->responds;

        $auth_user = Auth::user();

        if ($auth_user && $auth_user->id !== $this->object->user_id) {
            $notifiable->push($this->object->user);
        }

        return $notifiable->filter(function ($user, $key) {
            return $user->isNotifiable($this->path);
        });
    }
}
