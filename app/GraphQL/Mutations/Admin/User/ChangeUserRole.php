<?php

namespace App\GraphQL\Mutations\Admin\User;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Admin\User\ChangeUserRoleRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeUserRole
{
    use DynamicValidation;

    /**
     * @param null $rooValue
     * @param ChangeUserRoleRequest $args
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rooValue, ChangeUserRoleRequest $args, GraphQLContext $context)
    {
        $auth_user = $context->user();

        if ($auth_user->role !== User::ROLE_ROOT) {
            throw new GraphQLLogicRestrictException(__('user.permission_denied'), __('Error'));
        }

        $inputs = $args->validated();

        $changing_user_id = Arr::get($inputs, 'user_id');
        $role = Arr::get($inputs, 'role');

        $changing_user = User
            ::whereId($changing_user_id)
            ->firstOrFail();

        $changing_user->role = $role;

        if (!$changing_user->save()) {
            throw new GraphQLSaveDataException(__('user.update_failed'), __('Error'));
        }

        return $changing_user;
    }
}
