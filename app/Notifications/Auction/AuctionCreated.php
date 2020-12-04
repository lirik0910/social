<?php

namespace App\Notifications\Auction;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;

class AuctionCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'auction.created';

    /**
     * Started auction
     *
     * @var Auction
     */
    protected $auction;

    /**
     * Create a new notification instance.
     *
     * @param Auction $auction;
     * @return void
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
        $user = Auth::user()->id === $this->auction->user_id
            ? Auth::user()
            : $this->auction->user;

        return [
            'user' => NotificationsHelper::getNotificationUserData($user),
            'info' => [
                'auction_id' => (string) $this->auction->id
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
