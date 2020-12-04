<?php

namespace App\GraphQL\Queries\Chat;

use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ActiveRoomsCountUnreadedMessages
{
    /**
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context)
    {
        $user = $context->user();

        $rooms_ids = UsersPrivateChatRoom
            ::where(function ($query) use ($user) {
                $query
                    ->where('user_id', '=', $user->id)
                    ->orWhere('seller_id', '=', $user->id);
            })
            ->whereNull('ended_at')
            ->get()
            ->pluck('id')
            ->toArray();

        return UsersPrivateChatRoomMessage
            ::whereIn('room_id', $rooms_ids)
            ->where('user_id', '!=', $user->id)
            ->where('status', false)
            ->count();
    }
}
