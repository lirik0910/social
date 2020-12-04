<?php

namespace App\GraphQL\Mutations\Chat;

use App\Events\User\BalanceChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\NotificationsHelper;
use App\Http\Requests\Chat\CreateRoomRequest;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\User;

class CreateRoom
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    protected $seller_user;

    /**
     * @param $rootValue
     * @param CreateRoomRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateChatRoom
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateRoomRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $seller_id = Arr::get($inputs, 'user_id');
        $message = Arr::get($inputs, 'message');

        $this->user = $context->user();

        $this->seller_user = User
            ::where('id',$seller_id)
            ->with('profile')
            ->firstOrFail();

        if($this->user->balance < $this->seller_user->profile->chat_price){
            throw new GraphQLLogicRestrictException(__('chat.not_enough_money_in_the_account'), __('Error'));
        }

        $room = UsersPrivateChatRoom
            ::where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query
                            ->where('user_id', $this->user->id)
                            ->where('seller_id', $this->seller_user->id);
                    })
                    ->orWhere(function ($query) {
                        $query
                            ->where('user_id', $this->seller_user->id)
                            ->where('seller_id', $this->user->id);
                    });
            })
            ->whereNull('ended_at')
            ->first();

        if (empty($room) && $this->seller_user->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
            throw new GraphQLLogicRestrictException(__('privacy.restriction_for_you'), __('Error!'));
        }

        // User cannot create chat room with another if chat room is already existed and the user is seller.
        if (!empty($room) && $this->user->id === $room->seller_id) {
            throw new GraphQLLogicRestrictException(__('chat.room_access_denied'), __('Error'));
        }

        if(empty($room)) {
            $room = new UsersPrivateChatRoom($inputs);
            $room->seller_id = $this->seller_user->id;
            $room->status = true;
            $room->price = $this->seller_user->profile->chat_price;

            if (!$this->user->chat_rooms()->save($room)) {
                throw new GraphQLSaveDataException(__('chat.create_room_failed'), __('Error'));
            }
        }

        $this->createMessage($room, $message);

        if($room->user_id == $this->user->id) {
            $this->user->decrement('balance', $room->price);
            $this->seller_user->increment('balance',$room->price);
            $room->increment('amount',$room->price);

            foreach ([$this->user, $this->seller_user] as $user) {
                event(new BalanceChanged($user));
            }
        }

        return $room;
    }

    /**
     * Create message for passed room
     *
     * @param $room
     * @param $message
     * @throws GraphQLSaveDataException
     */
    protected function createMessage($room, $message)
    {
        $msg = new UsersPrivateChatRoomMessage();
        $msg->room_id = $room->id;
        $msg->user_id = $this->user->id;
        $msg->message = $message;
        $msg->price = $room->price;
        $msg->type = UsersPrivateChatRoomMessage::TYPE_MESSAGE;
        $msg->status = false;

        // MongoDB
        if (!$msg->save()) {
            throw new GraphQLSaveDataException(__('admin.cannot_save_log'), __('Error'));
        }

        NotificationsHelper::handle(['created'], $msg, 'usersPrivateChatRoomMessage');
    }
}
