<?php


namespace App\Libraries\GraphQL;

use App\Helpers\NotificationsHelper;
use Illuminate\Support\Facades\Notification;

abstract class AbstractNotificationSending
{
    protected $notifiable;
    protected $notification;
    protected $path;
    protected $object;

    /**
     * Determine if push notification needed
     *
     * @var bool
     */
    protected $push = false;

    /**
     * Determine if push notification has high priority
     *
     * @var bool
     */
    protected $high_priority = false;

    /**
     * AbstractNotificationSending constructor.
     * @param $notification
     * @param string $path
     * @param $object
     */
    public function __construct($notification, string $path, $object)
    {
        $this->notification = $notification;
        $this->path = $path;
        $this->object = $object;
    }

    public function handle()
    {
        $this->notifiable = $this->getNotifiable();
        $this->send();
    }

    abstract protected function getNotifiable();

    protected function send()
    {
        if(count($this->notifiable) > 0) {
            $notification = new $this->notification($this->object);

            Notification::send($this->notifiable, $notification);

            if ($this->push) {
                NotificationsHelper::pushNotification($this->notifiable, $notification->data, $this->high_priority);
            }
        }
    }
}
