<?php

namespace App\Http\Controllers;

use App\Filters\ScheduleFilter;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Schedule::class);

        $schedule = Schedule::where('deleted', 0)->get();
        if ($request->hasAny(['filter'])) {
            $filters = new ScheduleFilter($request);
            $schedule = Schedule::filter($filters)->get();
        }
        return response()->json(ScheduleResource::collection($schedule));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Schedule::class);

        $validatedData = $request->validate([
            'route_id'    => 'required|uuid|exists:routes,id',
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
            $schedule = Schedule::where('deleted', 0)->findOrFail($id);
            $this->authorize('delete', $schedule);

            $schedule->deleted = 1;
            $schedule->save();
            return response()->json(['message' => 'Schedule deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Schedule not found.'], Response::HTTP_NOT_FOUND);
        }
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No ids provided.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $schedules = Schedule::whereIn('id', $ids)->get();
            // Kiểm tra quyền 
            foreach ($schedules as $schedule) {
                $this->authorize('delete', $schedule);
                // Kiểm tra bài viết tồn tại và chưa bị xóa trước đó
                if (!$schedule || $schedule->deleted) {
                    return response()->json(['message' => 'One or more records do not exist or have already been deleted.'], Response::HTTP_NOT_FOUND);
                }
            }
            Schedule::whereIn('id', $ids)->update(['deleted' => 1]);

            return response()->json(['message' => 'Schedules deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
