<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PublicStream;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicStreamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any public streams.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the public stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PublicStream  $publicStream
     * @return mixed
     */
    public function view(User $user, PublicStream $publicStream)
    {
        //
    }

    /**
     * Determine whether the user can create public streams.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the public stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PublicStream  $publicStream
     * @return mixed
     */
    public function update(User $user, PublicStream $publicStream)
    {
        return $user->id === $publicStream->user_id;
    }

    /**
     * Determine whether the user can delete the public stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PublicStream  $publicStream
     * @return mixed
     */
    public function delete(User $user, PublicStream $publicStream)
    {
        //
    }

    /**
     * Determine whether the user can restore the public stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PublicStream  $publicStream
     * @return mixed
     */
    public function restore(User $user, PublicStream $publicStream)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the public stream.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PublicStream  $publicStream
     * @return mixed
     */
    public function forceDelete(User $user, PublicStream $publicStream)
    {
        //
    }

    /**
     * Determine wheter the user can start the public stream
     *
     * @param User $user
     * @param PublicStream $publicStream
     * @return bool
     */
    public function start(User $user, PublicStream $publicStream)
    {
        return $user->id === $publicStream->user->id;
    }
}
