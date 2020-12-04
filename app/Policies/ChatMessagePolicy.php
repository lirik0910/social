<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UsersPrivateChatRoomMessage;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatMessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users pivate chat room messages.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the users pivate chat room message.
     *
     * @param User $user
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return mixed
     */
    public function view(User $user, UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
    }

    /**
     * Determine whether the user can create users pivate chat room messages.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the users pivate chat room message.
     *
     * @param User $user
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return mixed
     */
    public function update(User $user, UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //return $user->id !== $usersPrivateChatRoomMessage->user_id;
    }

    /**
     * Determine whether the user can delete the users pivate chat room message.
     *
     * @param User $user
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return mixed
     */
    public function delete(User $user, UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        return $user->id === $usersPrivateChatRoomMessage->user_id;
    }

    /**
     * Determine whether the user can restore the users pivate chat room message.
     *
     * @param User $user
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return mixed
     */
    public function restore(User $user, UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the users pivate chat room message.
     *
     * @param User $user
     * @param UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage
     * @return mixed
     */
    public function forceDelete(User $user, UsersPrivateChatRoomMessage $usersPrivateChatRoomMessage)
    {
        //
    }
}
