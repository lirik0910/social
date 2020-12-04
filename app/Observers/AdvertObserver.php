<?php

namespace App\Observers;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\FavoriteHelper;
use App\Models\Advert;
use App\Models\SubscriberUserPublications;

class AdvertObserver
{
    /**
     * Handle the media "created" event.
     *
     * @param  \App\Models\Advert  $advert
     * @return void
     * @throws GraphQLSaveDataException
     */
    public function created(Advert $advert)
    {
        FavoriteHelper::createPublication(SubscriberUserPublications::PUB_TYPE_ADVERTS, $advert);
    }
}
