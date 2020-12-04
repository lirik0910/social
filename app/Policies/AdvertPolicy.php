<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Advert;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any adverts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the advert.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return mixed
     */
    public function view(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can create adverts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the advert.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return mixed
     */
    public function update(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can delete the advert.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return mixed
     */
    public function delete(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can restore the advert.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return mixed
     */
    public function restore(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the advert.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return mixed
     */
    public function forceDelete(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can create respond for advert
     *
     * @param User $user
     * @param Advert $advert
     * @return bool
     */
    public function createRespond(User $user, Advert $advert)
    {
        return $user->id !== $advert->user_id;
    }

    /**
     * Determine whether the user can approve respond for advert
     *
     * @param User $user
     * @param Advert $advert
     * @return bool
     */
    public function approveRespond(User $user, Advert $advert)
    {
        return $user->id === $advert->user_id;
    }

    /**
     * Determine whether the user can cancel advert
     *
     * @param User $user
     * @param Advert $advert
     * @return bool
     */
    public function cancel(User $user, Advert $advert)
    {
        return $user->id === $advert->user_id;
    }
}
