<?php

namespace App\GraphQL\Mutations\Admin\User;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\User\ChangeAdminPermissionRequest;
use App\Models\AdminToPermission;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddPermissionForUser
{
    use DynamicValidation;

    /**
     * @param null $_
     * @param ChangeAdminPermissionRequest $args
     * @param GraphQLContext $context
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException|\ReflectionException
     */
    protected function resolve($_, ChangeAdminPermissionRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('toggle_permission', $user);

        $inputs = $args->validated();

        $permission_id = Arr::get($inputs, 'permission_id');
        $changed_user_id = Arr::get($inputs, 'user_id');

        if (AdminToPermission::where(['user_id' => $changed_user_id, 'permission_id' => $permission_id])->exists()) {
            throw new GraphQLLogicRestrictException(__('user.permission_is_already_exist'), __('Error'));
        }

        $user_permission = new AdminToPermission();

        $user_permission->user_id = $changed_user_id;
        $user_permission->permission_id = $permission_id;

        if (!$user_permission->save()) {
            throw new GraphQLSaveDataException(__('user.failed_to_add_permission_to_admin'), __('Error'));
        }

        return true;
    }
}
