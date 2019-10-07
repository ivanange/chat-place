<?php

namespace App\Policies;

use App\User;
use App\Message;
use App\chat;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any messages.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function view(User $user,  Chat $chat)
    {
        return $chat->hasParticipant($user->id);
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Chat $chat)
    {
        $participant =  $chat->chackParticipant($user->id);
        return !$participant->isEmpty() and $participant->pivot->permissions & user::POST;
    }

    /**
     * Determine whether the user can update the message. 
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function update(User $user, Message $message, Chat $chat)
    {
        return  $message->owner($user->id)
                ->where("cid", "=", $chat->id)
                ->first() ? false : true;
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function delete(User $user, Message $message, Chat $chat)
    {
        return  $chat->hasParticipant($user->id) and 
                (   !$message-owner($user->id)
                    ->where("cid", "=", $chat->id)
                    ->first()
                    ->isEmpty() or 
                        ($chat->getParticipant($user->id)
                        ->pivot
                        ->permissions & DELETE_USER_MESSAGE )
                )   ? true : false;
    }

    /**
     * Determine whether the user can restore the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function restore(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the message.
     *
     * @param  \App\User  $user
     * @param  \App\Message  $message
     * @return mixed
     */
    public function forceDelete(User $user, Message $message)
    {
        //
    }
}
