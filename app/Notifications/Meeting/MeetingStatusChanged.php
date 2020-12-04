<?php

namespace App\Notifications\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MeetingStatusChanged extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'meeting.status_changed';

    /**
     * Accepted meeting
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     * Create a new notification instance.
     *
     * @param Meeting $meeting
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * @param mixed $notifiable
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public function toArray($notifiable)
    {
        $this->data = $this->getData($notifiable);

        return parent::toArray($notifiable);
    }

    /**
     * @param $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     * @throws GraphQLLogicRestrictException
     */
    public function toBroadcast($notifiable)
    {
        $this->data = $this->getData($notifiable);

        return parent::toBroadcast($notifiable);
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $who_change = $notifiable->id === $this->meeting->seller_id ? $this->meeting->user : $this->meeting->seller;

        return [
            'user' => NotificationsHelper::getNotificationUserData($who_change),
            'info' => [
                'meeting_id' => (string) $this->meeting->id,
                'meeting_status' => $this->meeting->status,
                'meeting_seller_id' => (string) $this->meeting->seller_id,
                'meeting_user_id' => (string) $this->meeting->user_id,
            ],
            'type' => $this->getEventType(),
        ];
    }

    /**
     * Return event type
     *
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    protected function getEventType()
    {
        switch ($this->meeting->status) {
            case Meeting::STATUS_ACCEPTED:
                $type = 'meeting.accepted';
                break;
            case Meeting::STATUS_CONFIRMED:
                $type = 'meeting.confirmed';
                break;
            case Meeting::STATUS_DECLINED:
                $type = 'meeting.declined';
                break;
            case Meeting::STATUS_FAILED:
                $type = 'meeting.failed';
                break;
            default:
                throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error!'));
        }

        return $type;
    }
}
