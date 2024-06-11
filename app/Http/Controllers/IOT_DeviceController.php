<?php

namespace App\Http\Controllers;

use App\Filters\IOT_DeviceFilter;
use App\Models\IOT_Device;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\IOT_DeviceResource;
use Illuminate\Validation\ValidationException;

class IOT_DeviceController extends Controller
{
    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $devices = IOT_Device::where('deleted', 0)->latest()->get()->filter(function ($device) use ($user) {
            return $user->can('view', $device);
        });
        if ($request->hasAny(['filter'])) {
            $filters = new IOT_DeviceFilter($request);
            $devices = IOT_Device::filter($filters)->get();
        }
        return response()->json(IOT_DeviceResource::collection($devices));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', IOT_Device::class);
            $validatedData = $request->validate([
                'home_id' => 'sometimes|exist:homes,id',
                'ip' => 'sometimes|string|max:50',
                'air_val' => 'sometimes|numeric',
                'left_status' => 'sometimes|numeric',
                'right_status' => 'sometimes|numeric',
                'status' => 'sometimes|string|max:100',
            ]);
            $device = IOT_Device::create([
                'home_id' => data_get($validatedData, 'home_id'),
                'ip' => data_get($validatedData, 'ip'),
                'air_val' => data_get($validatedData, 'air_val'),
                'left_status' => data_get($validatedData, 'left_status'),
                'right_status' => data_get($validatedData, 'right_status'),
                'status' => data_get($validatedData, 'right_status'),
            ]);

            return new IOT_DeviceResource($device);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $device = IOT_Device::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $device)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new IOT_Device($device);
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
            $device = IOT_Device::where('deleted', 0)->findOrFail($id);

            if (!auth()->user()->can('update', $device)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }

            $validatedData = $request->validate([
                'home_id' => 'sometimes|exist:homes,id',
                'ip' => 'sometimes|string|50',
                'air_val' => 'sometimes|numeric',
                'left_status' => 'sometimes|numeric',
                'right_status' => 'sometimes|numeric',
                'status' => 'sometimes|string|100',
            ]);

            $device->update($validatedData);

            return new IOT_DeviceResource($device);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $device = IOT_Device::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $device)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $device->deleted = 1;
            $device->save();
            return response()->json(['message' => 'Post deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
