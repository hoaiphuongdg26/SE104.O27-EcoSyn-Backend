<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $reports = Report::where('deleted', 0)->latest()->get()->filter(function ($report) use ($user) {
            return $user->can('view', $report);
        });
        return response()->json(ReportResource::collection($reports));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|in:user,device',
            'user_id' => 'required_if:type,user|exists:users,id',
            'device_id' => 'required_if:type,device|exists:devices,id',
            'description' => 'sometimes|string',
            'vote' => 'sometimes|int',
            'status' => 'sometimes|string|in:pending,in progress,resolved',
        ]);

        $report = Report::create([
            'created_by' => $request->user()->id,
            'role_of_creator' => $request->user()->roles->pluck('name')->first(),
            'type' => data_get($validatedData, 'type'),
            'user_id' => data_get($validatedData, 'user_id'),
            'device_id' => data_get($validatedData, 'device_id'),
            'description' => data_get($validatedData, 'description'),
            'vote' => data_get($validatedData, 'vote'),
            'status' => data_get($validatedData, 'status'),
        ]);

        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $report = Report::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $report)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new ReportResource($report);
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
            $report = Report::where('deleted', 0)->findOrFail($id);

            if (!auth()->user()->can('update', $report)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }

            $validatedData = $request->validate([
                'type' => 'sometimes|string|in:user,device',
                'user_id' => 'required_if:type,user|exists:users,id',
                'device_id' => 'required_if:type,device|exists:devices,id',
                'description' => 'sometimes|string',
                'vote' => 'sometimes|int',
                'status' => 'sometimes|string|in:pending,in progress,resolved',
            ]);

            $report->update($validatedData);

            return new ReportResource($report);
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
            $report = Report::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $report)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $report->deleted = 1;
            $report->save();
            return response()->json(['message' => 'Report deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
    public function bulkDelete(Request $request){

    }
}
