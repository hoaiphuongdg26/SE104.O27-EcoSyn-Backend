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
        $routes = Route::all();
        if ($request->hasAny(['filter'])) {
            $filters = new RouteFilter($request);
            $routes = Route::filter($filters)->get();
        }
        return RouteResource::collection($routes);
    }
    public function store(Request $request)
    {
        $this->authorize('create', Route::class);

        $validatedData = $request->validate([
            'start_home' => 'required|uuid|exists:homes,id',
            'end_home' => 'required|uuid|exists:homes,id',
            'status_id' => 'required|uuid|exists:statuses,id',
        ]);
        $route = Route::create($validatedData);
        return $route;
    }
    public function show($id)
    {
        try {
            //$id = $request->id;
            $route = Route::findOrFail($id);
            $this->authorize('view', $route);
            return $route;
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(Request $request, string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('update', $route);

        $validatedData = $request->validate([
            'start_home' => 'sometimes|uuid|exists:homes,id',
            'end_home' => 'sometimes|uuid|exists:homes,id',
            'status_id' => 'sometimes|uuid|exists:statuses,id',
        ]);

        $route->update($validatedData);

        return $route;
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
            return response()->json(['message' => 'Post deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
