<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PrivateStream;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateStreamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any private streams.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the private stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PrivateStream  $privateStream
     * @return mixed
     */
    public function view(User $user, PrivateStream $privateStream)
    {
        //
    }

    /**
     * Determine whether the user can create private streams.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the private stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PrivateStream  $privateStream
     * @return mixed
     */
    public function update(User $user, PrivateStream $privateStream)
    {
        return $user->id !== $privateStream->seller_id;
    }

    /**
     * Determine whether the user can delete the private stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PrivateStream  $privateStream
     * @return mixed
     */
    public function delete(User $user, PrivateStream $privateStream)
    {
        //
    }

    /**
     * Determine whether the user can restore the private stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PrivateStream  $privateStream
     * @return mixed
     */
    public function restore(User $user, PrivateStream $privateStream)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the private stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PrivateStream  $privateStream
     * @return mixed
     */
    public function forceDelete(User $user, PrivateStream $privateStream)
    {
        //
    }

    /**
     * Determine whether the user can create message
     *
     * @param User $user
     * @param PrivateStream $privateStream
     * @return mixed
     */
    public function createMessage(User $user, PrivateStream $privateStream)
    {
        return $user->id === $privateStream->user_id || $user->id === $privateStream->seller_id;
    }
}
