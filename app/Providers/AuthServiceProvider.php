<?php

namespace App\Providers;

use App\Models\Advert;
use App\Models\Auction;
use App\Models\Media;
use App\Models\PublicStream;
use App\Models\Meeting;
use App\Models\PersonalMessage;
use App\Models\PrivateStream;
use App\Models\Report;
use App\Models\Support;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Models\WantWithYou;
use App\Policies\AdvertPolicy;
use App\Policies\AuctionPolicy;
use App\Policies\ChatMessagePolicy;
use App\Policies\MediaPolicy;
use App\Policies\MeetingPolicy;
use App\Policies\ReportPolicy;
use App\Policies\SupportPolicy;
use App\Policies\UsersPrivateChatRoomPolicy;
use App\Policies\WantWithYouPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Support::class => SupportPolicy::class,
        Media::class => MediaPolicy::class,
        Meeting::class => MeetingPolicy::class,
        WantWithYou::class => WantWithYouPolicy::class,
        Report::class => ReportPolicy::class,
        Auction::class => AuctionPolicy::class,
        Advert::class => AdvertPolicy::class,
        UsersPrivateChatRoom::class => UsersPrivateChatRoomPolicy::class,
        UsersPrivateChatRoomMessage::class => ChatMessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function ($router) {
            $router->forAccessTokens();
        });
    }
}
