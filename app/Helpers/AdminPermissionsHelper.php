<?php


namespace App\Helpers;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\AdminPermission;
use App\Models\AdminToPermission;
use App\Models\User;
use Illuminate\Support\Arr;

class AdminPermissionsHelper
{
    /**
     * Check if the user has permission for passed action
     *
     * @param string $action
     * @param $user
     * @return void
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     */
    public static function check(string $action, $user)
    {
        if ($user->role !== User::ROLE_ROOT) {
            $current_permission = AdminPermission
                ::where('action', $action)
                ->first();

            if (is_null($current_permission)) {
                throw new GraphQLLogicRestrictException(__('user.incorrect_admin_permission'), __('Error'));
            }

            if (!AdminToPermission::where(['user_id' => $user->id, 'permission_id' => $current_permission->id])->exists()) {
                throw new GraphQLLogicRestrictException(__('user.permission_denied'), __('Error'));
            }
        }
    }
}
