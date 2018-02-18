<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the modelsAddress.
     *
     * @param  \App\Models\User $user
     * @param Address $address
     * @return mixed
     */
    public function view(User $user, Address $address)
    {
        return $address->domain->user_id == $user->id;
    }

    /**
     * Determine whether the user can create modelsAddresses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the modelsAddress.
     *
     * @param  \App\Models\User $user
     * @param Address $address
     * @return mixed
     */
    public function update(User $user, Address $address)
    {
        return $this->view($user, $address);
    }

    /**
     * Determine whether the user can delete the modelsAddress.
     *
     * @param  \App\Models\User $user
     * @param Address $address
     * @return mixed
     */
    public function delete(User $user, Address $address)
    {
        return false;
    }
}
