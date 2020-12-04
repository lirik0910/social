<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SendAdvertMeetingCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;
    protected $high_priority = true;

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        $auth_user = Auth::user();

        $users = $auth_user->id === $this->object->user_id
            ? [$auth_user, $this->object->seller]
            : [$auth_user, $this->object->user];

        $notifiable = Arr::where($users, function ($value) {
            return $value->isNotifiable($this->path);
        });

        return !empty($notifiable) ? Collection::wrap($notifiable) : collect([]);
    }
}
