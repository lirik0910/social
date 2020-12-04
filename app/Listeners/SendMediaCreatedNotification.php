<?php

namespace App\Listeners;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Notifications\Media\MediaCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMediaCreatedNotification
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
     * @throws GraphQLLogicRestrictException
     */
    public function handle($event)
    {
        $media = $event->media;
        $path = $event::EVENT_TYPE;

        $notifiable = $media->user->subscribers->filter(function ($user, $key) use ($path) {
            return $user->isNotifiable($path);
        });

        if(count($notifiable) > 0) {
            Notification::send($notifiable, (new MediaCreated($media, $event->count)));
        }
    }
}
