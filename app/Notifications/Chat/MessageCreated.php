<?php

namespace App\Notifications\Chat;

use App\Helpers\NotificationsHelper;
use App\Libraries\GraphQL\AbstractNotification;
use App\Models\User;
use App\Models\UsersPrivateChatRoomMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;

class MessageCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'chat_room_message.created';

    /**
     * Created message
     *
     * @var UsersPrivateChatRoomMessage
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param UsersPrivateChatRoomMessage $message
     * @return void
     */
    public function __construct(UsersPrivateChatRoomMessage $message)
    {
        $this->message = $message;
        $this->data = $this->getData();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [];
    }

    /**
     * @param null $notifiable
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function getData($notifiable = null)
    {
        $user = Auth::user()->id === $this->message->user_id
            ? Auth::user()
            : User::whereId($this->message->user_id)->first();

        return [
            'user' => NotificationsHelper::getNotificationUserData($user),
            'info' => [
                'room_id' => $this->message->room_id,
                'message_id' => (string) $this->message->id,
                'message_created_at' => $this->message->created_at,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
