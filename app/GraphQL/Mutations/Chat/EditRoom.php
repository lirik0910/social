<?php

namespace App\GraphQL\Mutations\Chat;

use App\Events\ChatRoomEditedEvent;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Chat\EditRoomRequest;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EditRoom
{
    use DynamicValidation;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UsersPrivateChatRoom
     */
    protected $room;

    /**
     * @param $rootValue
     * @param EditRoomRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateChatRoom
     * @throws GraphQLSaveDataException|GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, EditRoomRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $this->room = UsersPrivateChatRoom
            ::where('id', (int) $inputs['room_id'])
            ->whereNull('ended_at')
            ->firstOrFail();

        if($this->room->user_id == $this->user->id){
            throw new GraphQLLogicRestrictException(__('chat.room_access_denied'), __('Error'));
        }

        $this->room->price = $inputs['price'];
        $this->room->status = false;

        if (!$this->room->save()) {
            throw new GraphQLSaveDataException(__('chat.update_room_failed'), __('Error'));
        }

        $this->createPriceChangedMessage();

        $event_data = [
            'id' => $this->room->id,
            'price' => $this->room->price,
            'status' => $this->room->status,
        ];

        event(new ChatRoomEditedEvent($event_data));

        return $this->room;
    }

    /**
     * Create message about chat price changed
     *
     * @throws GraphQLSaveDataException
     */
    protected function createPriceChangedMessage()
    {
        $msg = new UsersPrivateChatRoomMessage();
        $msg->type = UsersPrivateChatRoomMessage::TYPE_PRICE_CHANGED;
        $msg->room_id = $this->room->id;
        $msg->user_id = $this->user->id;
        $msg->message = __('chat.new_price') . ' ' . $this->room->price;
        $msg->price = $this->room->price;
        $msg->status = false;

        // MongoDB
        if (!$msg->save()) {
            throw new GraphQLSaveDataException(__('chat.create_message_failed'), __('Error'));
        }
    }
}
