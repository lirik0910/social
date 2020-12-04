<?php

namespace App\GraphQL\Mutations\Chat;

use App\Events\ChatRoomEditedEvent;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateToDefaultRoomPrice
{
    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @return bool
     * @throws GraphQLSaveDataException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context)
    {
        $this->user = $context->user();

        $default_chat_price = $this->user->profile->chat_price;

        $rooms = UsersPrivateChatRoom
            ::where('seller_id', $this->user->id)
            ->whereNull('ended_at')
            ->get();

        $filtered_rooms = $rooms->filter(function ($room, $key) use ($default_chat_price) {
            return $room->price != $default_chat_price;
        });

        $rooms_ids = $filtered_rooms
            ->pluck('id')
            ->toArray();

        UsersPrivateChatRoom
            ::whereIn('id', $rooms_ids)
            ->update([
                'price' => $default_chat_price,
                'status' => false,
            ]);

        $this->createPriceChangedMessages($rooms_ids, $default_chat_price);

        foreach ($filtered_rooms as $room) {
            $room->refresh();

            $event_data = [
                'id' => $room->id,
                'price' => $room->price,
                'status' => (boolean) $room->status,
            ];

            event(new ChatRoomEditedEvent($event_data));
        }

        return true;
    }

    /**
     * Create price changed messages for changed  private chats
     *
     * @param $rooms_ids
     * @param $default_chat_price
     * @throws GraphQLSaveDataException
     */
    protected function createPriceChangedMessages($rooms_ids, $default_chat_price)
    {
        foreach ($rooms_ids as $room_id) {
            $msg = new UsersPrivateChatRoomMessage();
            $msg->room_id = $room_id;
            $msg->user_id = $this->user->id;
            $msg->message = __('chat.new_price') . ' ' . $default_chat_price;
            $msg->price = $default_chat_price;
            $msg->type = UsersPrivateChatRoomMessage::TYPE_PRICE_CHANGED;
            $msg->status = false;

            // MongoDB
            if (!$msg->save()) {
                throw new GraphQLSaveDataException(__('chat.create_message_failed'), __('Error'));
            }
        }
    }
}
