<?php

namespace App\Http\Middleware;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBannedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->hasFlag(User::FLAG_USER_BANNED) || !empty($user->ban_id)) {
            throw new GraphQLLogicRestrictException(__('user.banned'), __('Error!'));
        }

        return $next($request);
    }
}
