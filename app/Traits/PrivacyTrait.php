<?php

namespace App\Traits;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait PrivacyTrait
{
    /**
     * @param $query
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function setIgnoredUsers($query)
    {
        $block_ids = $this->getBlockIds();

        if (get_class($query->getModel()) !== User::class) {
            $query->whereHas('user', function ($q) use ($block_ids) {
                $q->whereNotIn('users.id', $block_ids);
                $q->whereRaw('(flags & ' . User::FLAG_PRIVATE_PROFILE . ') = 0');
            });
        } else {
            $query->whereNotIn('users.id', $block_ids);
            $query->whereRaw('(users.flags & ' . User::FLAG_PRIVATE_PROFILE . ') = 0');
        }


        return $query;
    }

    /**
     * Get privacy params for current (authorized) user
     *
     * @return array
     */
    protected function getBlockIds()
    {
        $user = $this->user ?? Auth::user();

        return BlockedUser
            ::where('blocked_id', '=', $user->id)
            ->get(['user_id'])
            ->pluck('user_id');
    }
}
