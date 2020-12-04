<?php

namespace App\Notifications\Auction;

use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Auction;


class WinnerBanned extends AbstractNotification
{
    const EVENT_TYPE = 'auction.winner_banned';

    /**
     * @var Auction
     */
    protected $auction;

    /**
     * Create a new notification instance.
     *
     * @param Auction $auction
     * @return void
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        return [
            'info' => [
                'auction_id' => (string) $this->auction->id,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
