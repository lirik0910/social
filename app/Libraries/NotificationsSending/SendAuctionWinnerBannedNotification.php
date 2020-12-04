<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;

class SendAuctionWinnerBannedNotification extends AbstractNotificationSending
{
    protected $push = true;

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        return $this->object->user;
    }
}
