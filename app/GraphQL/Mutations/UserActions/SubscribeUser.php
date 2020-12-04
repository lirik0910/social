<?php

namespace App\GraphQL\Mutations\UserActions;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Subscribe;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SubscribeUser
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $subscribe_user_id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $subscribe_user = User::whereId($subscribe_user_id)->firstOrFail();

        // Check action`s availability to this user
        $subscribe_user->isBlocked();

        //Current subscribe record
        $subscribe_record = Subscribe
            ::where(['user_id' => $subscribe_user_id, 'subscriber_id' => $user->id])
            ->exists();

        if ($subscribe_record) {
            throw new GraphQLLogicRestrictException(__('profile.subscribe_cannot'), __('Error'));
        } else {
            $subscribe_record = new Subscribe();
            $subscribe_record->user_id = $subscribe_user_id;
            $subscribe_record->subscriber_id = $user->id;

            if (!$subscribe_record->save()) {
                throw new GraphQLSaveDataException(__('profile.subscribe_failed'), __('Error'));
            }

            $user->increment('subscribes_count');
            $subscribe_user->increment('subscribers_count');
        }

        return [
            'subscribe_user' => $subscribe_user,
            'subscriber' => $user
        ];
    }
}
