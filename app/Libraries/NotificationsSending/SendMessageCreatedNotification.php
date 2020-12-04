<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SendMessageCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;
    protected $high_priority = true;

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        $room = UsersPrivateChatRoom
            ::whereId($this->object->room_id)
            ->first();

        if (!empty($room)) {
            $notifiable_id = Auth::user()->id === $room->user_id
                ? $room->seller_id
                : $room->user_id;

            $notifiable = User
                ::whereId($notifiable_id)
                ->first();
        }

        return !empty($notifiable) && $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
    }
}
