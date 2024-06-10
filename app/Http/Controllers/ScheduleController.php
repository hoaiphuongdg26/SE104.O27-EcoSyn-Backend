<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Resources\V1\PostResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $schedules = Schedule::latest()->get()->filter(function ($schedule) use ($user) {
        return $user->can('view', $schedule);
    });

    return response()->json($schedules);
    }

    public function store(Request $request)
    {
         $validatedData = $request->validate([
            'route_id'    => 'required|exists:routes,id',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date',
        ]);
        $staff_id = $request->user()->id;
        $schedule = Schedule::create([
            'staff_id'   => $staff_id,
            'route_id'   => $validatedData['route_id'],
            'start_time' => $validatedData['start_time'],
            'end_time'   => $validatedData['end_time'],
        ]);

        return response()->json($schedule, 201);
    }
    public function show(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            if (!auth()->user()->can('view', $schedule)) {
                return response()->json(['message' => 'You do not have permission for this action.'], Response::HTTP_FORBIDDEN);
            }
            return response()->json($schedule);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            if (!auth()->user()->can('update', $schedule)) {
                return response()->json(['message' => 'You do not have permission for this action.'], Response::HTTP_FORBIDDEN);
            }
            $validatedData = $request->validate([
                'route_id'    => 'sometimes|exists:routes,id',
                'start_time'  => 'sometimes|date',
                'end_time'    => 'sometimes|date',
            ]);
            $schedule->update($validatedData);
            return response()->json($schedule);
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
            if (!auth()->user()->can('delete', $schedule)) {
                return response()->json(['message' => 'You do not have permission for this action.'], Response::HTTP_FORBIDDEN);
            }
            $schedule->deleted = 1;
            $schedule->save();

            return response()->json(['message' => 'Schedule deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        }
    }
}
