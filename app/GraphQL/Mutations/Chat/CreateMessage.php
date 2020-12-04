<?php

namespace App\GraphQL\Mutations\Chat;

use App\Events\User\BalanceChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\NotificationsHelper;
use App\Http\Requests\Chat\CreateMessageRequest;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateMessage
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateMessageRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateChatRoomMessage
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateMessageRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $room = UsersPrivateChatRoom
            ::where('id', (int) $inputs['room_id'])
            ->whereNull('ended_at')
            ->firstOrFail();

        if (!$context->user()->can('view', $room) || ($user->id === $room->user_id && !$room->status)) {
            throw new GraphQLSaveDataException(__('chat.room_access_denied'), __('Error'));
        }

        if($room->user_id == $user->id  && $user->balance < $room->price) {
            throw new GraphQLLogicRestrictException(__('chat.not_enough_money_in_the_account'), __('Error'));
        }

        $msg = new UsersPrivateChatRoomMessage();
        $msg->type = UsersPrivateChatRoomMessage::TYPE_MESSAGE;
        $msg->room_id = $room->id;
        $msg->user_id = $user->id;
        $msg->message = $inputs['message'];
        $msg->price = $room->price;
        $msg->status = false;

        // MongoDB
        if (!$msg->save()) {
            throw new GraphQLSaveDataException(__('chat.create_message_failed'), __('Error'));
        }

        NotificationsHelper::handle(['created'], $msg, 'usersPrivateChatRoomMessage');

        if($room->user_id == $user->id) {
            $user->decrement('balance', $room->price);
            $room->seller->increment('balance', $room->price);
            $room->increment('amount', $room->price);

            foreach ([$user, $room->seller] as $user) {
                event(new BalanceChanged($user));
            }
        }

        return $msg;
    }
}
