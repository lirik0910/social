<?php

namespace App\GraphQL\Queries\Admin\User;

use App\Helpers\AdminPermissionsHelper;
use App\Models\AdminToPermission;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserAdminPermissions
{
    /**
     * @param null $_
     * @param array<string, mixed> $args
     * @param GraphQLContext $context
     */
    public function __invoke($_, array $args, GraphQLContext $context)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('permissions', $user);

        return AdminToPermission
            ::where('user_id', $user->id)
            ->get();
    }
}
