<?php

namespace App\Jobs;

use App\Helpers\NotificationsHelper;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MeetingRateNeededNotificationSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $meeting;

    /**
     * Create a new job instance.
     *
     * @param Meeting $meeting;
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->connection = 'notifications';
        $this->queue = 'notifications';
        $this->delay = $this->meeting->meeting_date->addDay();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationsHelper::handle(['rate_needed'], $this->meeting, 'meeting');
    }
}
