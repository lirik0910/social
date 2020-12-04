<?php

namespace App\GraphQL\Mutations\Chat;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateMessage
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $msg = UsersPrivateChatRoomMessage
            ::where('id', $inputs['id'])
            ->firstOrFail();

        $room = UsersPrivateChatRoom
            ::whereId($msg->room_id)
            ->whereNull('ended_at')
            ->firstOrFail();

        if (in_array($user->id, [$room->user_id, $room->seller_id]) && $user->id !== $msg->user_id) {
            throw new GraphQLSaveDataException(__('chat.access_denied'), __('Error'));
        }

        $msg->update(['status' => true]);

        return $msg;
    }
}
