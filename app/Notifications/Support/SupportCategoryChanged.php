<?php

namespace App\Notifications\Support;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\Support;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;

class SupportCategoryChanged extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'support.message_category_changed';

    /**
     * Support
     *
     * @var Support
     */
    protected $support;

    /**
     * Create a new notification instance.
     *
     * @param Support $support
     * @return void
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public function __construct(Support $support)
    {
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
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
