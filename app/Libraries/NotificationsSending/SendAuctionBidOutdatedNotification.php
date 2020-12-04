<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\AuctionBid;
use Illuminate\Database\Eloquent\Collection;

class SendAuctionBidOutdatedNotification extends AbstractNotificationSending
{
    protected $push = true;
    protected $high_priority = true;

    protected function getNotifiable()
    {
        $previous_bid = AuctionBid
            ::where('auction_id', '=', $this->object->auction_id)
            ->where('id', '!=', $this->object->id)
            ->orderByDesc('id')
            ->first();

        $previous_bid_user = empty($previous_bid) || $this->object->user_id === $previous_bid->user_id
            ? null
            : $previous_bid->user;

        return ($previous_bid_user && $previous_bid_user->isNotifiable($this->path))
            ? Collection::wrap($previous_bid_user)
            : collect([]);
    }
}
