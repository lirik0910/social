<?php

namespace App\Notifications\Auction;

use App\Helpers\NotificationsHelper;
use App\Models\AuctionBid;
use Illuminate\Bus\Queueable;
use App\Libraries\GraphQL\AbstractNotification;
use Illuminate\Support\Facades\Auth;

class AuctionBidCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'auction_bid.created';

    protected $auction_bid;

    /**
     * Create a new notification instance.
     *
     * @param $auction_bid
     * @return void
     */
    public function __construct(AuctionBid $auction_bid)
    {
        $this->auction_bid = $auction_bid;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        $user = Auth::user()->id === $this->auction_bid->user_id
            ? Auth::user()
            : $this->auction_bid->user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($user),
            'info' => [
                'auction_id' => (string) $this->auction_bid->auction_id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
