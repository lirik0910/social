<?php

namespace App\Observers;

use App\Helpers\FavoriteHelper;
use App\Models\Auction;
use App\Models\SubscriberUserPublications;

class AuctionsSubscribers
{
    public function created(Auction $auction)
    {
        FavoriteHelper::createPublication(SubscriberUserPublications::PUB_TYPE_AUCTIONS, $auction);
    }
}
