<?php

namespace App\GraphQL\Mutations\Admin\User;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\User\TogglePermissionRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class TogglePermission
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  TogglePermissionRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, TogglePermissionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('admin_permissions', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs,'user_id');
        $permission = Arr::get($inputs, 'permission');

        $modifying_user = $user->id == $id
            ? $user
            : User
                ::whereId($id)
                ->where('role', '!=', User::ROLE_USER)
                ->firstOrFail();

        $modifying_user->togglePermission((int) $permission);

        if (!$modifying_user->save()) {
            throw new GraphQLSaveDataException(__('user.update_failed'), __('Error'));
        }

        return $modifying_user;
    }
}
