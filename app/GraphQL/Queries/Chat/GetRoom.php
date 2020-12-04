<?php

namespace App\GraphQL\Queries\Chat;

use App\Http\Requests\Chat\GetRoomRequest;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetRoom
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param GetRoomRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateChatRoomMessage
     */
    protected function resolve($rootValue, GetRoomRequest $args, GraphQLContext $context)
    {
        $another_user_id = Arr::get($args->validated(), 'user_id');

        $user = $context->user();

        return UsersPrivateChatRoom
            ::where(function ($query) use ($user, $another_user_id) {
                $query
                    ->where(function ($query) use ($user, $another_user_id) {
                        $query
                            ->where('user_id', $user->id)
                            ->where('seller_id', $another_user_id);
                    })
                    ->orWhere(function ($query) use ($user, $another_user_id) {
                        $query
                            ->where('user_id', $another_user_id)
                            ->where('seller_id', $user->id);
                    });
            })
            ->whereNull('ended_at')
            ->first();
    }
}
