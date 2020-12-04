<?php

namespace App\Notifications\Support;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Support;
use App\Models\SupportMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;

class SupportMessageCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'support.message_created';

    /**
     * Support message
     *
     * @var SupportMessage
     */
    protected $support_message;

    /**
     * Support
     *
     * @var Support
     */
    protected $support;

    /**
     * Create a new notification instance.
     *
     * @param SupportMessage $support_message
     * @param Support $support
     * @return void
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public function __construct(SupportMessage $support_message, Support $support)
    {
        $this->support_message = $support_message;
        $this->support = $support;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        return [
            'info' => [
                'support_id' => (string) $this->support->id,
                'support_category' => $this->support->category,
                'support_status' => $this->support->status,
                'support_message' => $this->support_message,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
