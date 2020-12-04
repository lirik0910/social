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

class UnsubscribeUser
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $subscribe_user_id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $subscribe_user = User::whereId($subscribe_user_id)->firstOrFail();

        //Current subscribe record
        $subscribe_record = Subscribe
            ::where(['user_id' => $subscribe_user_id, 'subscriber_id' => $user->id])
            ->exists();

        if (!$subscribe_record) {
            throw new GraphQLLogicRestrictException(__('profile.unsubscribe_cannot'), __('Error'));
        } else {
            if (!Subscribe::where(['user_id' => $subscribe_user_id, 'subscriber_id' => $user->id])->delete()) {
                throw new GraphQLSaveDataException(__('profile.unsubscribe_failed'), __('Error'));
            }

            $user->decrement('subscribes_count');
            $subscribe_user->decrement('subscribers_count');
        }

        return [
            'subscribe_user' => $subscribe_user,
            'subscriber' => $user
        ];
    }
}
