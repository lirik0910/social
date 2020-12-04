<?php

namespace App\Http\Middleware;

use App\Exceptions\GraphQLLogicRestrictException;
use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FlagMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $param
     * @return mixed
     * * @throws GraphQLLogicRestrictException
     */
    public function handle($request, Closure $next, $param)
    {
        $user = Auth::user();

        foreach(explode(":", $param) as $flag){
            switch($flag) {
                case 'REQUIRED_PHONE_VERIFICATION':
                    if ($user->hasFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION)) {
                        throw new GraphQLLogicRestrictException(__('flag.'.$flag), __('Error'));
                    }
                    break;
                case 'REQUIRED_FILL_PROFILE':
                    if ($user->hasFlag(User::FLAG_REQUIRED_FILL_PROFILE)) {
                        throw new GraphQLLogicRestrictException(__('flag.'.$flag), __('Error'));
                    }
                    break;
                case 'PRIVATE_PROFILE':
                    if ($user->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
                        throw new GraphQLLogicRestrictException(__('flag.'.$flag), __('Error'));
                    }
                    break;
                case 'PHOTO_VERIFIED_PENDING':
                    if ($user->hasFlag(User::FLAG_PHOTO_VERIFIED_PENDING)) {
                        throw new GraphQLLogicRestrictException(__('flag.'.$flag), __('Error'));
                    }
                    break;
                case 'PHOTO_VERIFIED':
                    if (!$user->hasFlag(User::FLAG_PHOTO_VERIFIED)) {
                        throw new GraphQLLogicRestrictException(__('flag.'.$flag), __('Error'));
                    }
                    break;
            }
        }

        return $next($request);
    }
}
