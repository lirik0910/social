<?php

namespace App\GraphQL\Mutations\UserActions;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use Illuminate\Support\Arr;
use App\Models\BlockedUser;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UnblockUserByRowId
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
        $id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $block_record = BlockedUser
            ::where(['id' => $id, 'user_id' => $user->id])
            ->firstOrFail();

        if (!$block_record->delete()) {
            throw new GraphQLSaveDataException(__('profile.unblock_failed'), __('Error'));
        }

        $user->decrement('blocked_count');

        return $user->blocked_count;
    }
}
