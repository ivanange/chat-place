<?php

namespace App\Policies;

use App\User;
use App\Chat;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any chats.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the chat.
     *
     * @param  \App\User  $user
     * @param  \App\Chat  $chat
     * @return mixed
     */
    public function view(User $user, Chat $chat)
    {
        return $chat->type !== chat::OPEN_G ? $chat->hasParticipant($user->id) : true;
    }

    /**
     * Determine whether the user can create chats.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the chat.
     *
     * @param  \App\User  $user
     * @param  \App\Chat  $chat
     * @return mixed
     */
    public function update(User $user, Chat $chat)
    {
        $participant = $chat->checkParticipant($user->id); 
        if( $participant ) {
            $permissions = $participant->pivot->permissions ;
            return ( $permissions & user::ADMIN  or 
                     $permissions & user::SUPER_ADMIN or 
                     $permissions & user::CREATOR );
        } 
        else {
            return false;
        }
                
    }

    /**
     * Determine whether the user can delete the chat.
     *
     * @param  \App\User  $user
     * @param  \App\Chat  $chat
     * @return mixed
     */
    public function delete(User $user, Chat $chat)
    {
        $participant = $chat->checkParticipant($user->id); 
        if( $participant ) {
            $permissions = $participant->pivot->permissions ;
            return ( $permissions & user::DELETE_CHAT );
        } 
        else {
            return false;
        }
    }

    public function addUser(User $user, Chat $chat)
    {
        $participant = $chat->checkParticipant($user->id); 
        if( $participant ) {
            $permissions = $participant->pivot->permissions ;
            return ( $permissions & user::ADD_USER );
        } 
        else {
            return false;
        }
    }

    public function updateUser(User $user, Chat $chat, User $participant)
    {
        $user = $chat->checkParticipant($user->id); 
        if( $user ) {
            $permissions = $user->pivot->permissions ;
            return  $participant->isAdmin($chat) ?
                    $permissions & user::EDIT_ADMIN_PERMISSIONS :
                    (   $participant->isSuperAdmin($chat) ? 
                        $permissions & user::CREATOR :
                        $permissions & user::EDIT_PERMISSIONS
                    ); 
        } 
        else {
            return false;
        }
    }

    public function removeUser(User $user, Chat $chat, User $participant)
    {
        $user = $chat->checkParticipant($user->id); 
        if( $user ) {
            $permissions = $user->pivot->permissions ;
            return  $participant->isAdmin($chat) ?
                    $permissions & user::REMOVE_USER and $user->isSuperAdmin($chat) :
                    (   $participant->isSuperAdmin($chat) ? 
                        $permissions & user::CREATOR and $permissions & user::REMOVE_USER :
                        $permissions & user::REMOVE_USER
                    ); 
        } 
        else {
            return false;
        }
    }


    public function viewUser(User $user, Chat $chat)
    {
        return  $chat->type !== chat::OPEN_G ? $chat->hasParticipant($user->id) : true;
    }

    /**
     * Determine whether the user can restore the chat.
     *
     * @param  \App\User  $user
     * @param  \App\Chat  $chat
     * @return mixed
     */
    public function restore(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the chat.
     *
     * @param  \App\User  $user
     * @param  \App\Chat  $chat
     * @return mixed
     */
    public function forceDelete(User $user, Chat $chat)
    {
        //
    }
}
