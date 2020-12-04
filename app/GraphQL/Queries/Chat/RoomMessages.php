<?php

namespace App\GraphQL\Queries\Chat;

use App\Http\Requests\Chat\RoomMessagesRequest;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use GraphQL\Type\Definition\ResolveInfo;

class RoomMessages extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Chat room ID
     *
     * @var integer|string
     */
    protected $room_id;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param RoomMessagesRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, RoomMessagesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->room_id = Arr::get($inputs, 'id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'dir' => 'DESC',
            'column' => 'created_at'
        ];

        $room = UsersPrivateChatRoom
            ::where('id', $this->room_id)
            ->firstOrFail();

        $user = $context->user();

        if(!$user->can('view', $room)){
            throw new GraphQLSaveDataException(__('chat.room_access_denied'), __('Error'));
        }

        return [
            'room' => $room,
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return UsersPrivateChatRoomMessage::where('room_id', '=', (int) $this->room_id);
    }

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return int
     */
    protected function getTotal($rootValue, IDRequiredRequest $args)
    {
        $this->room_id = Arr::get($args->validated(), 'id');

        return $this->getResultsTotalCount();
    }
}
