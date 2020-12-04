<?php

namespace App\Providers;

use App\Events\Media\AvatarUploaded;
use App\Events\Media\AvatarVerifyingApprove;
use App\Events\Media\AvatarVerifyingReject;
use App\Events\Media\AvatarVerifyingRequest;
use App\Events\Media\MediaCreated;
use App\Events\User\AvatarChanged;
use App\Helpers\EventHelper;
use App\Listeners\ChangeUserVerifyingFlags;

use App\Listeners\SendMediaCreatedNotification;
use App\Models\Advert;
use App\Models\Auction;
use App\Models\BlockedUser;
use App\Models\Media;
use App\Models\UsersPrivateChatRoomMessage;

use App\Observers\AdvertObserver;
use App\Observers\AuctionsSubscribers;
use App\Observers\BlockedUserObserver;
use App\Observers\MediaObserver;
use App\Observers\ChatMessageObserver;
use App\Models\Meeting;
use App\Observers\MeetingObserver;


use Event;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;



class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        AvatarUploaded::class => [
            ChangeUserVerifyingFlags::class,
        ],
        AvatarChanged::class => [
            ChangeUserVerifyingFlags::class,
        ],
        AvatarVerifyingRequest::class => [
            ChangeUserVerifyingFlags::class,
        ],
        AvatarVerifyingApprove::class => [
            ChangeUserVerifyingFlags::class
        ],
        AvatarVerifyingReject::class => [
            ChangeUserVerifyingFlags::class
        ],
        MediaCreated::class => [
            SendMediaCreatedNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Auction::observe(AuctionsSubscribers::class);
        Advert::observe(AdvertObserver::class);
        Media::observe(MediaObserver::class);
        Meeting::observe(MeetingObserver::class);
        UsersPrivateChatRoomMessage::observe(ChatMessageObserver::class);
        BlockedUser::observe(BlockedUserObserver::class);

        Event::listen('eloquent.created: *', function ($eventName, $object) {
            EventHelper::handle($eventName,$object);
        });

        Event::listen('eloquent.updated: *', function ($eventName, $object) {
            EventHelper::handle($eventName,$object);
        });

        Event::listen('eloquent.deleted: *', function ($eventName, $object) {
            EventHelper::handle($eventName,$object);
        });
    }
}
