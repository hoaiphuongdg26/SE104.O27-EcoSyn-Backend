<?php

namespace App\Http\Controllers;

use App\Models\IOT_Device;
use Illuminate\Http\Request;

class IOT_DeviceController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            //'ip' => 'required|ipv4',
            'ip' => 'required|string',
            'air_val' => 'required|numeric',
            'left_status' => 'required|numeric',
            'right_status' => 'required|numeric',
            'status' => 'required|string',
        ]);
        $iotDevices = IOT_Device::create($validatedData);
        return response()->json(['message' => 'Data stored successfully'], 201);
    }
    public function index()
    {
        $iotDevices = IOT_Device::all();
        return response()->json($iotDevices);
    }
    public function detail($id)
    {
        $iotDevice = IOT_Device::findOrFail($id);
        return response()->json($iotDevice);
    }
}
