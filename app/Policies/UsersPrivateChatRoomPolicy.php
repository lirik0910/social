<?php

namespace App\Policies;

use App\Helpers\ChatRoomHelper;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPrivateChatRoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users private chat rooms.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the users private chat room.
     *
     * @param User $user
     * @param UsersPrivateChatRoom $usersPrivateChatRoom
     * @return mixed
     */
    public function view(User $user, UsersPrivateChatRoom $usersPrivateChatRoom)
    {
        return in_array($user->id, [$usersPrivateChatRoom->user_id, $usersPrivateChatRoom->seller_id]);
    }

    /**
     * Determine whether the user can create users private chat rooms.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the users private chat room.
     *
     * @param User $user
     * @param UsersPrivateChatRoom $usersPrivateChatRoom
     * @return mixed
     */
    public function update(User $user, UsersPrivateChatRoom $usersPrivateChatRoom)
    {
        //
    }

    /**
     * Determine whether the user can delete the users private chat room.
     *
     * @param User $user
     * @param UsersPrivateChatRoom $usersPrivateChatRoom
     * @return mixed
     */
    public function delete(User $user, UsersPrivateChatRoom $usersPrivateChatRoom)
    {
        //
    }

    /**
     * Determine whether the user can restore the users private chat room.
     *
     * @param User $user
     * @param UsersPrivateChatRoom $usersPrivateChatRoom
     * @return mixed
     */
    public function restore(User $user, UsersPrivateChatRoom $usersPrivateChatRoom)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the users private chat room.
     *
     * @param User $user
     * @param UsersPrivateChatRoom $usersPrivateChatRoom
     * @return mixed
     */
    public function forceDelete(User $user, UsersPrivateChatRoom $usersPrivateChatRoom)
    {
        //
    }
}
