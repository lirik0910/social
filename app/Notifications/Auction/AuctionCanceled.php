<?php

namespace App\Notifications\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class AuctionCanceled extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'auction.cancelled';

    /**
     * @var Auction
     */
    protected $auction;

    /**
     * Create a new notification instance.
     *
     * @param Auction $auction
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $user = Auth::user() && Auth::user()->id !== $this->auction->user_id
            ? null
            : Auth::user();

        return [
            'user' => $user,
            'info' => [
                'auction_id' => (string) $this->auction->id
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
