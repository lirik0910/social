<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Meeting;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any meetings.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function view(User $user, Meeting $meeting)
    {
        return $user->role !== User::ROLE_USER || $user->id === $meeting->seller_id || $user->id === $meeting->user_id;
    }

    /**
     * Determine whether the user can create meetings.
     *
     * @param \App\Models\User $user
     * @param mixed $args
     *
     * @return mixed
     */
    public function create(User $user, $args = [])
    {
        return (string) $user->id !== $args['seller_id'];
    }

    /**
     * Determine whether the user can update the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function update(User $user, Meeting $meeting)
    {
        return $user->id !== $meeting->seller_id;
    }

    /**
     * Determine whether the user can delete the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function delete(User $user, Meeting $meeting)
    {
        return $user->id === $meeting->seller_id && $meeting->status === Meeting::STATUS_NEW;
    }

    /**
     * Determine whether the user can restore the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function restore(User $user, Meeting $meeting)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function forceDelete(User $user, Meeting $meeting)
    {
        //
    }

    /**
     * Determine whether the user can accept the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function accept(User $user, Meeting $meeting)
    {
        return $user->id === $meeting->seller_id;
    }

    /**
     * Determine whether the user can confirm the meeting.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Meeting $meeting
     * @return mixed
     */
    public function confirm(User $user, Meeting $meeting)
    {
        return $user->id === $meeting->user_id;
    }

    /**
     * Determine whether the user can decline the meeting
     *
     * @param User $user
     * @param Meeting $meeting
     * @return bool
     */
    public function decline(User $user, Meeting $meeting)
    {
        return $user->id === $meeting->user_id || $user->id === $meeting->seller_id;

    }

    /**
     * Determine whether the user can create review for confirmed meeting
     *
     * @param User $user
     * @param Meeting $meeting
     * @return bool
     */
    public function createReview(User $user, Meeting $meeting)
    {
        return $user->id === $meeting->user_id || $user->id === $meeting->seller_id;
    }
}
