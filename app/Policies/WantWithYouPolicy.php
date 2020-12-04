<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WantWithYou;
use Illuminate\Auth\Access\HandlesAuthorization;

class WantWithYouPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any app models want with yous.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the app models want with you.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WantWithYou  $appModelsWantWithYou
     * @return mixed
     */
    public function view(User $user, WantWithYou $appModelsWantWithYou)
    {
        //
    }

    /**
     * Determine whether the user can create app models want with yous.
     *
     * @param  \App\Models\User  $user
     * @param array $args
     *
     * @return mixed
     */
    public function create(User $user, array $args = [])
    {
        return (string) $user->id !== $args['user_id'];
    }

    /**
     * Determine whether the user can update the app models want with you.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WantWithYou  $appModelsWantWithYou
     * @return mixed
     */
    public function update(User $user, WantWithYou $appModelsWantWithYou)
    {
        //
    }

    /**
     * Determine whether the user can delete the app models want with you.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WantWithYou  $appModelsWantWithYou
     * @return mixed
     */
    public function delete(User $user, WantWithYou $appModelsWantWithYou)
    {
        //
    }

    /**
     * Determine whether the user can restore the app models want with you.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WantWithYou  $appModelsWantWithYou
     * @return mixed
     */
    public function restore(User $user, WantWithYou $appModelsWantWithYou)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the app models want with you.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WantWithYou  $appModelsWantWithYou
     * @return mixed
     */
    public function forceDelete(User $user, WantWithYou $appModelsWantWithYou)
    {
        //
    }
}
