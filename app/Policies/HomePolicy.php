<?php

namespace App\Policies;

use App\Models\Home;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HomePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Home $home): bool
    {
        return $user->id === $home->customer_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Home $home): bool
    {
        return $user->id === $home->customer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Home $home): bool
    {
        return $user->id === $home->customer_id;
    }
}
