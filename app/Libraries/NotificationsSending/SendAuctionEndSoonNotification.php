<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class SendAuctionEndSoonNotification extends AbstractNotificationSending
{
    protected $push = true;

    public function getNotifiable()
    {
        $auth_user = Auth::user();

        if (!empty($auth_user) && $auth_user->id === $this->object->user_id) {
            $notifiable = Auth::user()->subscribers;
        } else {
            $notifiable = User
                ::leftJoin('subscribes', function ($join) {
                    $join->on('subscribes.subscriber_id', '=', 'users.id');
                })
                ->where('subscribes.user_id', '=', $this->object->user_id)
                ->get(['users.*']);
        }

        $bids = $this->object
            ->bids()
            ->distinct('user_id')
            ->get();

        $bids->load('user');

        $participants = $bids->map(function ($bid, $key) {
            return $bid->user;
        });

        if(count($participants) > 0) {
            $notifiable->merge($participants);
        }

        $notifiable = $notifiable->filter(function ($user, $key) {
            return $user->isNotifiable($this->path);
        });

        return $notifiable;
    }
}
