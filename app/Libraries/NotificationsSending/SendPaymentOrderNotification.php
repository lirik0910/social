<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SendPaymentOrderNotification extends AbstractNotificationSending
{
    protected $push = true;

    public function getNotifiable()
    {
        $notifiable = $this->object->user_id === Auth::user()->Id
            ? Auth::user()
            : $this->object->user;

        return Collection::wrap($notifiable);
    }
}
