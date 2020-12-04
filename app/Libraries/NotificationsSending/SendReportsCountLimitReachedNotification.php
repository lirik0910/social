<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SendReportsCountLimitReachedNotification extends AbstractNotificationSending
{
    protected $push = true;

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        $notifiable = $this->object;

        $last_period_reports = $notifiable
            ->received_reports()
            ->where('status', Report::STATUS_APPROVED)
            ->where(function ($query) {
                $query
                    ->where('created_at', '>=', Carbon::now()->subWeeks(2))
                    ->where('created_at', '<=', Carbon::now());
            })
            ->get();

        return count($last_period_reports) >= Report::LIMIT_COUNT_FOR_NOTIFICATION_SENDING ?  Collection::wrap($notifiable) : collect([]) ;
    }
}
