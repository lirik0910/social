<?php

namespace App\Observers;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\ChatRoomHelper;
use App\Helpers\NotificationsHelper;
use App\Models\UsersPrivateChatRoomMessage;

class ChatMessageObserver
{
    /**
     * Handle the models users private chat room message "created" event.
     *
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function created(UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        ChatRoomHelper::activeChatRoomEvent(ChatRoomHelper::CHAT_ROOM_EVENT_MESSAGE_CREATED, $usersPrivateChatRoomMessage);
        NotificationsHelper::handle(['created'], $usersPrivateChatRoomMessage, 'usersPrivateChatRoomMessage');
    }

    /**
     * Handle the models users private chat room message "updated" event.
     *
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return void
     * @throws GraphQLLogicRestrictException
     */
    public function updated(UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        ChatRoomHelper::activeChatRoomEvent(ChatRoomHelper::CHAT_ROOM_EVENT_MESSAGE_READED, $usersPrivateChatRoomMessage);
    }

    /**
     * Handle the models users private chat room message "deleted" event.
     *
     * @param  UsersPrivateChatRoomMessage  $usersPrivateChatRoomMessage
     * @return void
     */
    public function deleted(UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //
    }

    /**
     * Handle the models users private chat room message "restored" event.
     *
     * @param  UsersPrivateChatRoomMessage  $usersPrivateChatRoomMessage
     * @return void
     */
    public function restored(UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //
    }

    /**
     * Handle the models users private chat room message "force deleted" event.
     *
     * @param  UsersPrivateChatRoomMessage  $usersPrivateChatRoomMessage
     * @return void
     */
    public function forceDeleted(UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //
    }
}
