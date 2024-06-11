<?php

namespace App\Http\Controllers;

use App\Filters\RouteFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Route;
use App\Http\Resources\RouteResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Route::class);
        $routes = Route::where('deleted', 0)->get();
        if ($request->hasAny(['filter'])) {
            $filters = new RouteFilter($request);
            $routes = Route::filter($filters)->get();
        }
        return response()->json(RouteResource::collection($routes));
    }
    public function store(Request $request)
    {
        $this->authorize('create', Route::class);

        $validatedData = $request->validate([
            'start_home' => 'required|exists:homes,id',
            'end_home' => 'required|exists:homes,id',
            'status_id' => 'required|exists:statuses,id',
        ]);
        $route = Route::create($validatedData);
        return new RouteResource($route);
    }
    public function show($id)
    {
        try {
            //$id = $request->id;
            $route = Route::findOrFail($id);
            $this->authorize('view', $route);
            return new RouteResource($route);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(Request $request, string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('update', $route);

        $validatedData = $request->validate([
            'start_home' => 'sometimes|exists:homes,id',
            'end_home' => 'sometimes|exists:homes,id',
            'status_id' => 'sometimes|exists:statuses,id',
        ]);

        $route->update($validatedData);

        return new RouteResource($route);
    }

    // public function destroy(string $id)
    // {
    //     $route = Route::findOrFail($id);
    //     $this->authorize('delete', $route);
    //     $route->delete();
    //     return response()->json(null, 204);
    // }
    public function destroy(string $id)
    {
        try {
            $route = Route::where('deleted', 0)->findOrFail($id);
            $this->authorize('delete', $route);
            $route->deleted = 1;
            $route->save();
            return response()->json(['message' => 'Route deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No ids provided.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $routes = Route::whereIn('id', $ids)->get();
            // Kiểm tra quyền 
            foreach ($routes as $route) {
                $this->authorize('delete', $route);
                // Kiểm tra bài viết tồn tại và chưa bị xóa trước đó
                if (!$route || $route->deleted) {
                    return response()->json(['message' => 'One or more routes do not exist or have already been deleted.'], Response::HTTP_NOT_FOUND);
                }
            }
            Route::whereIn('id', $ids)->update(['deleted' => 1]);

            return response()->json(['message' => 'Routes deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
