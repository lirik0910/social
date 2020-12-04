<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\User;

class SendMeetingRateNeededNotification extends AbstractNotificationSending
{
    protected $push = true;

    /**
     * @return mixed
     */
    public function getNotifiable()
    {
        $meeting = $this->object;

        $notifiable = User
            ::leftJoin('meeting_reviews', function ($join) use($meeting){
                $join
                    ->on('users.id', '=', 'meeting_reviews.user_id')
                    ->where('meeting_reviews.meeting_id', '=', $meeting->id);
            })
            ->where(function($query) use($meeting) {
                $query->where('id', $meeting->user_id)
                    ->orWhere('id', $meeting->seller_id);
            })
            ->whereNull('meeting_reviews.id')
            ->get();


        $notifiable = $notifiable->filter(function ($user, $key) {
            return $user->isNotifiable($this->path);
        });

        return $notifiable;
    }
}
