<?php

namespace App\GraphQL\Queries\Admin\User;

use App\Models\AdminPermission;

class AdminPermissions
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return AdminPermission
            ::orderBy('title', 'ASC')
            ->get();
    }
}
