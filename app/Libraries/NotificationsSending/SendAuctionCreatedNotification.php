<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SendAuctionCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        if (Auth::user()->id === $this->object->user_id) {
            $subscribers = Auth::user()->subscribers;
        } else {
            $subscribers = User
                ::leftJoin('subscribes', function ($join) {
                    $join->on('subscribes.subscriber_id', '=', 'users.id');
                })
                ->where('subscribes.user_id', '=', $this->object->user_id)
                ->get(['users.*']);
        }

        return $subscribers->filter(function ($user, $key) {
            return $user->isNotifiable($this->path);
        });
    }
}
