<?php

namespace App\Jobs;

use App\Exceptions\GraphQLSaveDataException;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MeetingFailedStatusChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $meeting;

    /**
     * Create a new job instance.
     *
     * @param Meeting $meeting
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->connection = 'database';
        $this->delay = $this->meeting->meeting_date->addDay();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GraphQLSaveDataException
     */
    public function handle()
    {
        $this->meeting->status = Meeting::STATUS_FAILED;

        if(!$this->meeting->save()) {
            $message = 'Meeting with ID=' . $this->meeting->id .' was not saved after status changed on '. $this->meeting->status .'.';

            Log::channel('jobs_errors')->error($message);
        }
    }
}
