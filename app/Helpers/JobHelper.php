<?php


namespace App\Helpers;

use App\Jobs\AuctionEndSoonNotification;
use App\Jobs\AuctionMeetingCreate;
use App\Jobs\MeetingFailedStatusChange;
use App\Jobs\MeetingRateNeededNotificationSend;
use App\Jobs\MeetingRequestDelete;
use Illuminate\Support\Facades\DB;
use App\Notifications\Auction\AuctionEndSoon;
use App\Notifications\Meeting\MeetingStartSoon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class JobHelper
{
    public static function checkIfNeeded($job)
    {
        if(method_exists($job, 'payload')) {
            $payload = $job->payload();
            $className = $payload['displayName'];

            $notification = unserialize($payload['data']['command']);
        } else {
            $payload = json_decode($job->payload);
            $className = $payload->displayName;

            if(Str::startsWith($className, 'App\\Notifications\\')) {
                $notification = unserialize($payload->data->command)->notification;
            } else {
                $notification = unserialize($payload->data->command);
            }
        }

        $property_name = self::getPropertyName($className);

        return $notification->$property_name->isJobsNeeded($className);
    }

    /**
     * Return property name for passed job
     *
     * @param $className
     * @return mixed
     */
    protected static function getPropertyName($className)
    {
        $data = [
            MeetingStartSoon::class => 'meeting',
            MeetingRateNeededNotificationSend::class => 'meeting',
            MeetingRequestDelete::class => 'meeting',
            MeetingFailedStatusChange::class => 'meeting',
            AuctionEndSoonNotification::class => 'auction',
            AuctionMeetingCreate::class => 'auction'
        ];

        return Arr::get($data, $className);
    }
}
