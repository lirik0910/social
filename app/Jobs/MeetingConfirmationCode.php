<?php

namespace App\Jobs;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\NotificationsHelper;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MeetingConfirmationCode implements ShouldQueue
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
        $this->queue = 'notifications';
        $this->connection = 'notifications';
        $this->delay = $this->meeting->meeting_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GraphQLSaveDataException
     */
    public function handle()
    {
        $this->createConfirmationCode();

        NotificationsHelper::handle(['confirmation_code'], $this->meeting, 'meeting');
    }

    /**
     * @throws GraphQLSaveDataException
     */
    public function createConfirmationCode()
    {
        $code = (string) rand(111111, 999999);

        $this->meeting->confirmation_code = encrypt($code);

        if(!$this->meeting->save()) {
            $message = 'Meeting with ID=' . $this->meeting->id .' was not saved with confirmation code.';

            Log::channel('jobs_errors')->error($message);
        }
    }
}
