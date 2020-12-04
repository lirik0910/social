<?php

namespace App\Observers;

use App\Events\User\AvatarChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\FavoriteHelper;
use App\Models\Media;
use App\Models\SubscriberUserPublications;

class MediaObserver
{
    /**
     * Handle the media "created" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     * @throws GraphQLSaveDataException
     */
    public function created(Media $media)
    {
        FavoriteHelper::createPublication(SubscriberUserPublications::PUB_TYPE_MEDIA, $media);
    }

    /**
     * Handle the media "updated" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function updated(Media $media)
    {
        //
    }

    /**
     * Handle the media "deleted" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        $media_user = $media->user;

        if($media->type === Media::TYPE_AVATAR && $media_user->image === $media->name) {
            $next_media = Media
                ::where('type', Media::TYPE_AVATAR)
                ->where('user_id', $media->user_id)
                ->where(function ($q) {
                    $q->orWhere('status', '!=', Media::STATUS_BANNED);
                    $q->orWhereNull('status');
                })
                ->latest()
                ->first();

            $media_user->image = $next_media ? $next_media->name : null;

            // User will save in event listener (ChangeUserVerifyingFlags)
            event(new AvatarChanged($media_user, $next_media));
        }

        $media->favorite()->delete();
    }

    /**
     * Handle the media "restored" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function restored(Media $media)
    {
        //
    }

    /**
     * Handle the media "force deleted" event.
     *
     * @param  \App\Models\Media  $media
     * @return void
     */
    public function forceDeleted(Media $media)
    {
        //
    }
}
