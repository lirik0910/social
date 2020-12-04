<?php

namespace App\GraphQL\Mutations\Chat;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\ChatRoomHelper;
use App\Http\Requests\Chat\UpdateRoomMessagesRequest;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateRoomMessagesStatus
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @return boolean
     * @throws GraphQLSaveDataException
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        $room_id = Arr::get($args->validated(), 'id');

        $room = UsersPrivateChatRoom
            ::where('id', '=', (int) $room_id)
            ->whereNull('ended_at')
            ->firstOrFail();

        if (!$user->can('view', $room)) {
            throw new GraphQLSaveDataException(__('chat.room_access_denied'), __('Error'));
        }

        UsersPrivateChatRoomMessage
            ::where('room_id', '=', (int) $room_id)
            ->where('user_id', '!=', $user->id)
            ->update(['status' => true]);

        ChatRoomHelper::activeChatRoomEventRoom(ChatRoomHelper::CHAT_ROOM_EVENT_READED, $room);

        return true;
    }
}
