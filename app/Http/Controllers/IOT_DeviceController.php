<?php

namespace App\Http\Controllers;

use App\Models\IOT_Device;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\IOT_DeviceResource;

class IOT_DeviceController extends Controller
{
    public function index()
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $devices = IOT_Device::where('deleted', 0)->latest()->get()->filter(function ($device) use ($user) {
            return $user->can('view', $device);
        });
        return response()->json(IOT_DeviceResource::collection($devices));
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
