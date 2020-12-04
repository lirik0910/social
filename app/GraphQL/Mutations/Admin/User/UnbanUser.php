<?php

namespace App\GraphQL\Mutations\Admin\User;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Report;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UnbanUser
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('user_ban', $user);

        $id = Arr::get($args->validated(), 'id');

        $user = User
            ::whereId($id)
            ->firstOrFail();

        if (empty($user->ban_id)) {
            throw new GraphQLLogicRestrictException(__('user.already_not_banned'), __('Error!'));
        }

        $user->removeFlag(User::FLAG_USER_BANNED);
        $user->ban_id = null;

        if (!$user->save()) {
            throw new GraphQLSaveDataException(__('user.update_failed'), __('Error!'));
        }

        return $user;
    }
}
