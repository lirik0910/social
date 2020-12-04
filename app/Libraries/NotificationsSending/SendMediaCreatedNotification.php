<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SendMediaCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    /**
     * @return mixed
     */
    protected function getNotifiable()
    {
        $media_user = Auth::user() === $this->object->user_id
            ? Auth::user()
            : $this->object->user;

        return $media_user->hasFlag(User::FLAG_PRIVATE_PROFILE)
            ? collect([])
            : $media_user->subscribers->filter(function ($user, $key) {
                    return $user->isNotifiable($this->path);
                });

    }
}
