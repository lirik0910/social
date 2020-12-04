<?php


namespace App\Libraries\NotificationsSending;


use App\Libraries\GraphQL\AbstractNotificationSending;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SendMeetingStatusChangedNotification extends AbstractNotificationSending
{
    protected $push = true;

    public function __construct($notification, string $path, $object)
    {
        parent::__construct($notification, $path, $object);

        if ($this->object->status === Meeting::STATUS_ACCEPTED) {
            $this->high_priority = true;
        }
    }


    protected function getNotifiable()
    {
        $auth_user = Auth::user();

        if (!empty($auth_user)) {
            $notifiable = $auth_user->id  === $this->object->seller_id ? $this->object->user : $this->object->seller;

            $notifiable = $notifiable->isNotifiable($this->path) ? Collection::wrap($notifiable) : collect([]);
        } else {
            $notifiable = collect([]);
        }

        return $notifiable;
    }
}
