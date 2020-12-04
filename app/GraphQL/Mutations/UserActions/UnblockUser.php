<?php

namespace App\GraphQL\Mutations\UserActions;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Http\Requests\User\Block\UnblockUserRequest;
use App\Models\BlockedUser;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UnblockUser
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return integer
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $blocked_user_id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $block_record = BlockedUser
            ::where([
                'blocked_id' => $blocked_user_id,
                'user_id' => $user->id
            ])
            ->firstOrFail();

        if (!$block_record->delete()) {
            throw new GraphQLSaveDataException(__('profile.unblock_failed'), __('Error'));
        }

        $user->decrement('blocked_count');

        return $user->blocked_count;
    }
}
