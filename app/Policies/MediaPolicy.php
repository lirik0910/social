<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Media;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any media.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Media  $media
     * @return mixed
     */
    public function view(User $user, Media $media)
    {
        //
    }

    /**
     * Determine whether the user can create media.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Media  $media
     * @return mixed
     */
    public function update(User $user, Media $media)
    {
        return $user->id === $media->user_id;
    }

    /**
     * Determine whether the user can delete the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Media  $media
     * @return mixed
     */
    public function delete(User $user, Media $media)
    {
        return $user->id === $media->user_id;
    }

    /**
     * Determine whether the user can restore the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Media  $media
     * @return mixed
     */
    public function restore(User $user, Media $media)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Media  $media
     * @return mixed
     */
    public function forceDelete(User $user, Media $media)
    {
        //
    }

    /**
     * Determine whether the user can view media presents
     *
     * @param User $user
     * @param Media $media
     * @return bool
     */
    public function viewPresents(User $user, Media $media)
    {
        return $user->id === $media->user_id;
    }

    /**
     * Determine whether the user can create media presents
     *
     * @param User $user
     * @param Media $media
     *
     * @return bool
     */
    public function createPresent(User $user, Media $media)
    {
        return $user->id !== $media->user_id;
    }
}
