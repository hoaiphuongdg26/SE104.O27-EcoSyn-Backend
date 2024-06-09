<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    // Lấy danh sách tất cả các schedule
        $schedules = Schedule::all();
        return response()->json($schedules);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Tạo một schedule mới
        $validatedData = $request->validate([
            'staff_id' => 'required|exists:staff,staff_id',
            'route_id' => 'required|exists:routes,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $schedule = Schedule::create($validatedData);
        return response()->json($schedule, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Lấy thông tin của một schedule cụ thể
        $schedule = Schedule::findOrFail($id);
        return response()->json($schedule);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'staff_id' => 'required|exists:staff,staff_id',
            'route_id' => 'required|exists:routes,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($validatedData);
        return response()->json($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Xóa một schedule
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return response()->json(null, 204);
    }
}
