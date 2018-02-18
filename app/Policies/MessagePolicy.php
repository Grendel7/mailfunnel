<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the message.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Message  $message
     * @return mixed
     */
    public function view(User $user, Message $message)
    {
        return $message->address->domain->user_id == $user->id;
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the message.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Message  $message
     * @return mixed
     */
    public function update(User $user, Message $message)
    {
        return $this->view($user, $message);
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Message  $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        return false;
    }
}
