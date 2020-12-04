<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisSubscriberJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:dispatch {job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen presence user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //        Log::alert('>>>' . Redis::get('presence-presence:members'));

        Redis::subscribe(['PresenceChannelUpdated'], function ($channel, $message) {
            Log::alert('>>> ' . $channel . ' - ' . $message);
        });
//
//        Redis::subscribe(['PresenceChannelJoin'], function ($message) {
//            Log::alert('>>>1 ' . $message);
//        });
//        Redis::subscribe(['PresenceChannelJoined'], function ($message) {
//            Log::alert('>>>2 ' . $message);
//        });
//
//        Redis::subscribe(['PresenceChannelLeave'], function ($message) {
//            Log::alert('>>>3 ' . $message);
//        });
//        Redis::subscribe(['PresenceChannelLeaved'], function ($message) {
//            Log::alert('>>>4 ' . $message);
//        });


    }
}
