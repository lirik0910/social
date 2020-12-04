<?php

namespace App\Notifications\Auction;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\AuctionBid;
use Illuminate\Support\Facades\Auth;

class AuctionBidOutdated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'auction_bid.outdated';

    /**
     * Auction who has new bid
     *
     * @var AuctionBid
     */
    protected $auction_bid;

    /**
     * Create a new notification instance.
     *
     * @param AuctionBid $auction_bid
     * @return void
     */
    public function __construct(AuctionBid $auction_bid)
    {
        $this->auction_bid = $auction_bid;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $auction_user = User
            ::whereId($this->auction_bid->auction_user_id)
            ->first();

        return [
            'user' => NotificationsHelper::getNotificationUserData($auction_user),
            'info' => [
                'auction_id' => (string) $this->auction_bid->auction_id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
