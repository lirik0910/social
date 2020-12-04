<?php

namespace App\Providers;

use App\Helpers\NotificationsHelper;
use App\Models\AdminPaymentTransaction;
use App\Models\Advert;
use App\Models\AdvertRespond;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\MediaPresent;
use App\Models\PaymentOrder;
use App\Models\User;
use App\Models\Media;
use App\Models\PublicStream;
use App\Models\Meeting;
use App\Models\UsersPrivateChatRoom;
use App\Observers\AuctionsSubscribers;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\PrivateStream;
use Illuminate\Support\Facades\Queue;
use App\Helpers\JobHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::ignoreMigrations();

        Relation::morphMap([
            'users' => User::class,
            'media' => Media::class,
            'meetings' => Meeting::class,
            'auctions' => Auction::class,
            'adverts' => Advert::class,
            'mediaPresents' => MediaPresent::class,
            'auctionBids' => AuctionBid::class,
            'advertResponds' => AdvertRespond::class,
            'paymentOrders' => PaymentOrder::class,
            'privateChatRooms' => UsersPrivateChatRoom::class,
            'adminPaymentTransactions' => AdminPaymentTransaction::class,
//            'public_streams' => PublicStream::class,
//            'private_streams' => PrivateStream::class,
        ]);

        Queue::before(function (JobProcessing $event) {
            if(array_search($event->job->getConnectionName(), ['notifications', 'database'])) {
                if(!JobHelper::checkIfNeeded($event->job)) {
                    $event->job->delete();
                }
            }
        });
    }
}
