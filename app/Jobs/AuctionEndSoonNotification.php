<?php

namespace App\Jobs;

use App\Helpers\NotificationsHelper;
use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AuctionEndSoonNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $auction;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     * @param Auction $auction
     * @return void
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
        $this->queue = 'notifications';
        $this->connection = 'notifications';
        $this->delay = $auction->end_at->subMinutes(30);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationsHelper::handle(['end_soon'], $this->auction, 'auction');
    }
}
