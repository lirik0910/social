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

class MeetingRequestDelete implements ShouldQueue
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
        $this->delay = $this->meeting->created_at->addMinutes(30);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws
     */
    public function handle()
    {
        if(!$this->meeting->delete()) {
            $message = 'Meeting with ID=' . $this->meeting->id .' and status '. $this->meeting->status .' was not deleted.';

            Log::channel('jobs_errors')->error($message);
        }
    }
}
