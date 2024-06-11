<?php

namespace App\Http\Controllers;

use App\Filters\ScheduleFilter;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Schedule::class);

        $schedules = Schedule::latest()->get();
        if ($request->hasAny(['filter'])) {
            $filters = new ScheduleFilter($request);
            $schedules = Schedule::filter($filters)->get();
        }
        return ScheduleResource::collection($schedules);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Schedule::class);

        $validatedData = $request->validate([
            'route_id'    => 'required|exists:routes,id',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date',
        ]);

        $schedule = Schedule::create([
            'staff_id'   => $request->user()->id,
            'route_id'   => data_get($validatedData, 'route_id'),
            'start_time' => data_get($validatedData, 'start_time'),
            'end_time'   => data_get($validatedData, 'end_time'),
        ]);

        return new ScheduleResource($schedule);
    }

    public function show(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $this->authorize('view', $schedule);

            return new ScheduleResource($schedule);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $this->authorize('update', $schedule);

            $validatedData = $request->validate([
                'route_id'    => 'sometimes|exists:routes,id',
                'start_time'  => 'sometimes|date',
                'end_time'    => 'sometimes|date',
            ]);

            $schedule->update($validatedData);
            return new ScheduleResource($schedule);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $this->authorize('delete', $schedule);

            $schedule->delete();
            return response()->json(['message' => 'Schedule deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        }
    }
}
