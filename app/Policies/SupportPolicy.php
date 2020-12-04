<?php

namespace App\Policies;

use App\Models\Support;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any supports.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the support.
     *
     * @param User $user
     * @param Support $support
     * @return mixed
     */
    public function view(User $user, Support $support)
    {
        return in_array($user->id, [$support->user_id, $support->moderator_id]);
    }

    /**
     * Determine whether the user can create supports.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the support.
     *
     * @param User $user
     * @param Support $support
     * @return mixed
     */
    public function update(User $user, Support $support)
    {
        //
    }

    /**
     * Determine whether the user can delete the support.
     *
     * @param User $user
     * @param Support $support
     * @return mixed
     */
    public function delete(User $user, Support $support)
    {
        //
    }

    /**
     * Determine whether the user can restore the support.
     *
     * @param User $user
     * @param Support $support
     * @return mixed
     */
    public function restore(User $user, Support $support)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the support.
     *
     * @param User $user
     * @param Support $support
     * @return mixed
     */
    public function forceDelete(User $user, Support $support)
    {
        //
    }

    /**
     * Determine whether the user can change category for support request
     *
     * @param User $user
     * @param Support $support
     * @return boolean
     */
    public function changeCategory(User $user, Support $support)
    {
        return is_null($support->moderator_id)
            ? $user->role !== User::ROLE_USER
            : $user->id === $support->moderator_id;
    }

    /**
     * Determine whether the user can change status for support request
     *
     * @param User $user
     * @param Support $support
     * @return boolean
     */
    public function changeStatus(User $user, Support $support)
    {
        return $support->status === Support::STATUS_IN_PROGRESS
            ? in_array($user->id, [$support->user_id, $support->moderator_id])
            : $user->role !== User::ROLE_USER && in_array($support->moderator_id, [null, $user->id]);
    }

    /**
     * Determine whether the user can change moderator for support request
     *
     * @param User $user
     * @param Support $support
     * @return boolean
     */
    public function changeModerator(User $user, Support $support)
    {
        return $user->id === $support->moderator_id;
    }
}
