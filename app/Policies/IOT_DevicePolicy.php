<?php

namespace App\Policies;

use App\Models\IOT_Device;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IOT_DevicePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IOT_Device $iOTDevice): bool
    {
        return $user->id === $iOTDevice->user();
    }
    public function update(User $user, IOT_Device $iOTDevice): bool
    {
        return !$user->hasRole('customer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IOT_Device $iOTDevice): bool
    {
        return !$user->hasRole('customer');
    }
}
