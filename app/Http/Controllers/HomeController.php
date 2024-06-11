<?php

namespace App\Http\Controllers;

use App\Filters\AddressFilter;
use App\Filters\HomeFilter;
use App\Filters\LocationFilter;
use App\Http\Resources\HomeResource;
use App\Models\Address;
use App\Models\Home;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $homes = Home::where('deleted', 0)->latest()->get()->filter(function ($home) use ($user) {
            return $user->can('view', $home);
        });
        if ($request->hasAny(['filter'])) {
            $homefilters = new HomeFilter($request);
            $homes = Home::filter($homefilters)->get();

            $addressfilters = new AddressFilter($request);
            $homes = Home::filter($addressfilters)->get();

            $locationfilters = new LocationFilter($request);
            $homes = Home::filter($locationfilters)->get();
        }
        return response()->json(HomeResource::collection($homes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'sometimes|nullable|exists:users,id',
            'unit_number' => 'sometimes|nullable|string|max:150',
            'street_number' => 'sometimes|nullable|string',
            'address_line' => 'sometimes|nullable|string|max:150',
            'ward' => 'sometimes|nullable|string|max:150',
            'district' => 'sometimes|nullable|string|max:150',
            'city' => 'sometimes|nullable|string|max:150',
            'province' => 'sometimes|nullable|string|max:150',
            'country_name' => 'sometimes|nullable|string|max:150',
            'longitude' => 'sometimes|nullable|numeric',
            'latitude' => 'sometimes|nullable|numeric',
        ]);
        $home = Home::create([
            'customer_id' => data_get($validatedData, 'customer_id'),
        ]);
        // Tạo mới một Address với ID tương ứng của Home
        $address = new Address([
            'unit_number' => data_get($validatedData, 'unit_number'),
            'street_number' => data_get($validatedData, 'street_number'),
            'address_line' => data_get($validatedData, 'address_line'),
            'ward' => data_get($validatedData, 'ward'),
            'district' => data_get($validatedData, 'district'),
            'city' => data_get($validatedData, 'city'),
            'province' => data_get($validatedData, 'province'),
            'country_name' => data_get($validatedData, 'country_name'),
        ]);
        $address->setAttribute('id', $home->id);
        $address->save();
        // Tạo mới một Location với ID tương ứng của Address
        $location = new Location([
            'longitude' => data_get($validatedData, 'longitude'),
            'latitude' => data_get($validatedData, 'latitude'),
        ]);
        $location->setAttribute('id', $address->id);
        $location->save();
        return new HomeResource($home);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $home = Home::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $home)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new HomeResource($home);
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
            $home = Home::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('update', $home)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }

            $validatedData = $request->validate([
                'customer_id' => 'sometimes|nullable|exists:users,id',
                'unit_number' => 'sometimes|nullable|string|max:150',
                'street_number' => 'sometimes|nullable|string',
                'address_line' => 'sometimes|nullable|string|max:150',
                'ward' => 'sometimes|nullable|string|max:150',
                'district' => 'sometimes|nullable|string|max:150',
                'city' => 'sometimes|nullable|string|max:150',
                'province' => 'sometimes|nullable|string|max:150',
                'country_name' => 'sometimes|nullable|string|max:150',
                'longitude' => 'sometimes|nullable|numeric',
                'latitude' => 'sometimes|nullable|numeric',
            ]);

            $home->update($validatedData);
            $address = Address::where('deleted', 0)->findOrFail($id);
            $address->update($validatedData);
            $location = Location::where('deleted', 0)->findOrFail($id);
            $location->update($validatedData);

            return new HomeResource($home);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Home not found'], Response::HTTP_NOT_FOUND);
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
            $home = Home::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $home)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $home->deleted = 1;
            $home->save();

            $address = Address::where('deleted', 0)->findOrFail($id);
            $address->deleted = 1;
            $address->save();

            $location = Location::where('deleted', 0)->findOrFail($id);
            $location->deleted =1;
            $location->save();

            return response()->json(['message' => 'Home deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
