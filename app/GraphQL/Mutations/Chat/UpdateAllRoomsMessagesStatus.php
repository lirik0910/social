<?php

namespace App\GraphQL\Mutations\Chat;

use App\Helpers\ChatRoomHelper;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateAllRoomsMessagesStatus
{
    /**
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @return boolean
     */
    public function resolve($rootValue, array $args, GraphQLContext $context)
    {
        $user = $context->user();

        $rooms = UsersPrivateChatRoom
            ::where(function ($query) use ($user) {
                $query
                    ->where('user_id', '=', $user->id)
                    ->orWhere('seller_id', '=', $user->id);
            })
            ->whereNull('ended_at')
            ->get();

        $rooms_ids = $rooms
            ->pluck('id')
            ->toArray();

        UsersPrivateChatRoomMessage::whereIn('room_id', $rooms_ids)
            ->where('user_id', '!=', $user->id)
            ->update(['status' => true]);

        foreach ($rooms as $room) {
            ChatRoomHelper::activeChatRoomEventRoom(ChatRoomHelper::CHAT_ROOM_EVENT_READED, $room);
        }

        return true;
    }
}
