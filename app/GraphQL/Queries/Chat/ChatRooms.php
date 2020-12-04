<?php

namespace App\GraphQL\Queries\Chat;

use App\Http\Requests\Chat\ActiveRoomsRequest;
use App\Http\Requests\Chat\ActiveRoomsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Traits\DynamicValidation;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use Auth;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class ChatRooms extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Selection`s filter
     *
     * @var array
     */
    protected $filter;

    /**
     * @param $rootValue
     * @param ActiveRoomsRequest $args
     * @param GraphQLContext $context
     * @return array
     */
    protected function resolve($rootValue, ActiveRoomsRequest $args, GraphQLContext $context)
    {
        $this->user = $context->user();

        $inputs = $args->validated();

        $this->filter = Arr::get($inputs, 'filter');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'column' => 'users_private_chat_rooms.updated_at',
            'dir' => 'DESC',
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = UsersPrivateChatRoom
            ::select('users_private_chat_rooms.*')
            ->join('users', 'users_private_chat_rooms.user_id', '=','users.id')
            ->join('users as sellers', 'users_private_chat_rooms.seller_id', '=','sellers.id');

        switch($this->filter['type']){
            case UsersPrivateChatRoom::VIEW_TYPE_ALL:
                $instance->where(function($query) {
                        $query->where('users_private_chat_rooms.user_id', $this->user->id);
                        $query->orWhere('users_private_chat_rooms.seller_id', $this->user->id);
                });
                break;
            case UsersPrivateChatRoom::VIEW_TYPE_AUTHOR:
                $instance
                    ->where('users_private_chat_rooms.user_id', $this->user->id)
                    ->whereNull('users_private_chat_rooms.ended_at');
                break;
            case UsersPrivateChatRoom::VIEW_TYPE_PARTICIPANT:
                $instance->where('users_private_chat_rooms.seller_id', $this->user->id);
                break;
        }

        if (isset($this->filter['is_closed'])) {
            if (!empty($this->filter['is_closed'])) {
                $instance->whereNotNull('ended_at');
            } else {
                $instance->whereNull('ended_at');
            }
        }

        if (!empty($this->filter['nickname'])) {
            $instance->where(function ($query) {
                $query->where('users.nickname', 'like', $this->filter['nickname'] . '%');
                $query->orWhere('sellers.nickname', 'like', $this->filter['nickname'] . '%');
            });
        }

        return $instance;
    }

    /**
     * Get results total count for base query
     *
     * @param $rootValue
     * @param ActiveRoomsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, ActiveRoomsTotalRequest $args)
    {
        $this->user = Auth::user();

        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
