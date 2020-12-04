<?php

namespace App\Listeners;

use App\Exceptions\GraphQLSaveDataException;
use App\Models\Media;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChangeUserVerifyingFlags
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     * @throws GraphQLSaveDataException
     */
    public function handle($event)
    {
        // Only avatars (media with type "Avatar") can be passed into that listener

        if(!$event->media || $event->user->image === $event->media->name) {
            $media_status = $event->media ? $event->media->status : null;

            switch($media_status) {
                case Media::STATUS_VERIFYING_PENDING:
                    $event->user->removeFlag(User::FLAG_PHOTO_VERIFIED);

                    if(!$event->user->hasFlag(User::FLAG_PHOTO_VERIFIED_PENDING)) {
                        $event->user->addFlag(User::FLAG_PHOTO_VERIFIED_PENDING);
                    }

                    break;
                case Media::STATUS_VERIFIED:
                    $event->user->removeFlag(User::FLAG_PHOTO_VERIFIED_PENDING);

                    if(!$event->user->hasFlag(User::FLAG_PHOTO_VERIFIED)) {
                        $event->user->addFlag(User::FLAG_PHOTO_VERIFIED);
                    }

                    break;
                default:
                    $event->user->removeFlag(User::FLAG_PHOTO_VERIFIED);
                    $event->user->removeFlag(User::FLAG_PHOTO_VERIFIED_PENDING);

                    break;
            }

            if(!$event->user->save()) {
                throw new GraphQLSaveDataException(__('user.update_failed'), __('Error'));
            }
        }
    }
}
