<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SendAdminPaymentTransactionCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        $notifiable = !empty(Auth::user()) && Auth::user()->id === $this->object->user_id
            ? Auth::user()
            : $this->object->user;

        return Collection::wrap($notifiable);
    }
}
