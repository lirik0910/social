<?php

namespace App\Jobs;

use App\Helpers\EventHelper;
use App\Helpers\NotificationsHelper;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AuctionMeetingCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $auction;

    /**
     * Create a new job instance.
     *
     * @param Auction $auction
     * @return void
     */
    public function __construct(Auction $auction)
    {
        $this->auction = $auction;
        $this->connection = 'database';
        $this->delay = $this->auction->end_at;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $last_bid = $this->auction->last_bid;

        if(!empty($last_bid)) {
            if (!$last_bid->user->hasFlag(User::FLAG_USER_BANNED)) {
                $seller_id = $this->auction->user_id;
                $user_id = $last_bid->user_id;

                $meeting_info = clone $this->auction;
                $meeting_info->price = $last_bid->value;
                $meeting_info->seller_id = $seller_id;
                $meeting_info->user_id = $user_id;
                $meeting_info->safe_deal = true;
                $meeting_info = $meeting_info->toArray();
                $meeting_info['status'] = Meeting::STATUS_ACCEPTED;

                if(!$this->auction->meeting()->create($meeting_info)) {
                    $message = 'Failed to create meeting for auction with ID='. $this->auction->id .'.';

                    Log::channel('jobs_errors')->error($message);
                }
            } else {
                $formatted_model_name = EventHelper::getFormattedModelName(get_class($this->auction));

                NotificationsHelper::handle(['winner_banned'], $this->auction, $formatted_model_name);
            }
        }
    }
}
