<?php

namespace App\Http\Middleware;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\LogsHelper;
use App\Models\GlobalLog;
use Closure;
use App\Models\User;

class AdminMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $param
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();

        if(!in_array($user->role, [User::ROLE_ROOT, User::ROLE_ADMIN, User::ROLE_MODERATOR, User::ROLE_STAFF])) {
            throw new GraphQLLogicRestrictException(__('admin.access_denied'), __('Error'));
        }

        // LOG ADMIN ACTIONS
        LogsHelper::createLog($user, $request->all());

        return $next($request);
    }
}

