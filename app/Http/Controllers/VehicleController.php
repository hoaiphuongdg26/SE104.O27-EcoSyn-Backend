<?php

namespace App\Http\Controllers;

use App\Events\VehicleDeleting;
use App\Filters\VehicleFilter;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();
        $this->authorize('view', Vehicle::class);
        $vehicles = Vehicle::where('deleted', 0)->latest()->get();
        if ($request->hasAny(['filter'])) {
            $filters = new VehicleFilter($request);
            $vehicles = Vehicle::filter($filters)->get();
        }
        return response()->json(VehicleResource::collection($vehicles));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'license_plate' => 'sometimes|nullable|string|max:150',
            'status' => 'sometimes|nullable|string|max:100',
        ]);

        $vehicle = Vehicle::create([
            'license_plate' => data_get($validatedData, 'license_plate'),
            'status' => data_get($validatedData, 'status'),
        ]);

        return new VehicleResource($vehicle);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vehicle = Vehicle::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $vehicle)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new VehicleResource($vehicle);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vehicle = Vehicle::where('deleted', 0)->findOrFail($id);

            if (!auth()->user()->can('update', $vehicle)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }

            $validatedData = $request->validate([
                'license_plate' => 'sometimes|nullable|string|max:150',
                'status' => 'sometimes|nullable|string|max:100',
            ]);

            $vehicle->update($validatedData);

            return new VehicleResource($vehicle);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehicle = Vehicle::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $vehicle)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            Event::dispatch(new VehicleDeleting($vehicle));
            $vehicle->deleted = 1;
            $vehicle->save();
            return response()->json(['message' => 'Vehicle deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
    public function attachUser(Request $request, Vehicle $vehicle)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Get the user by id
        $user = User::find($validatedData['user_id']);
        if (!$user->hasAnyRole(['admin', 'staff', 'super admin'])) {
            return response()->json([
                'message' => 'User does not have the required role to be attached to a vehicle.',
            ], 403);
        }
        // Attach the user to the vehicle
        $vehicle->staffs()->attach($user->id);

        return response()->json([
            'message' => 'User attached to vehicle successfully',
            'vehicle' => new VehicleResource($vehicle),
        ]);
    }
    public function getUsersByVehicle($vehicleId)
    {
        // Lấy vehicle theo id
        $vehicle = Vehicle::findOrFail($vehicleId);

        // Lấy tất cả các user thuộc về vehicle này
        $users = $vehicle->staffs()->get();

        return response()->json(UserResource::collection($users));
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No ids provided.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $vehicles = Vehicle::whereIn('id', $ids)->get();

            foreach ($vehicles as $vehicle) {
                $this->authorize('delete', $vehicle);

                if (!$vehicle || $vehicle->deleted) {
                    return response()->json(['message' => 'One or more vehicles do not exist or have already been deleted.'], Response::HTTP_NOT_FOUND);
                }
            }
            Vehicle::whereIn('id', $ids)->update(['deleted' => 1]);
            return response()->json(['message' => 'Vehicles deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
