<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use Illuminate\Database\Eloquent\Collection;

class SendAuctionBidCreatedNotification extends AbstractNotificationSending
{
    protected $push = true;

    protected function getNotifiable()
    {
        $notifiable = $this->object->auction_user;

        return $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
    }
}
