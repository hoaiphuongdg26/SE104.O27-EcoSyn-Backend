<?php

namespace App\Listeners;

use App\Events\VehicleDeleting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DeleteVehicleRelationships
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VehicleDeleting $event): void
    {
        $vehicle = $event->vehicle;
        foreach ($event->vehicle->staffs as $user) {
            $vehicle->staffs()->updateExistingPivot($user->id, ['deleted' => 1]);
        }
    }
}
