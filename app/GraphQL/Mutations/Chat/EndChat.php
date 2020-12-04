<?php

namespace App\GraphQL\Mutations\Chat;

use App\Events\ChatRoomEditedEvent;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\ChatRoomHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\UsersPrivateChatRoom;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EndChat
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $room = UsersPrivateChatRoom
            ::whereId((int) $id)
            ->firstOrFail();

        if (!$user->can('view', $room)) {
            throw new GraphQLLogicRestrictException(__('chat.room_access_denied'), __('Error'));
        }

        $room->ended_by_id = $user->id;
        $room->ended_at = Carbon::now();

        if (!$room->save()) {
            throw new GraphQLSaveDataException(__('chat.update_room_failed'), __('Error'));
        }

        ChatRoomHelper::activeChatRoomEventRoom(ChatRoomHelper::CHAT_ROOM_EVENT_ENDED, $room);

        return $room;
    }
}
