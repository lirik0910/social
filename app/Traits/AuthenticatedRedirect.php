<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

trait AuthenticatedRedirect
{
    public function generateAuthPath($user = NULL)
    {
        if (!$user)
            $user = \Auth::user();

        if ($user) {
            $user->auth_token = Str::random(60); // TODO: check whether auth token is unique in users table
            $user->auth_token_expire_at = Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s');
        }

        if ($user->save()) {
            return env('SPA_URL') . '/auth?token=' . $user->auth_token;
        }

        \Auth::guard()->logout();

        return route('login');
    }
}
