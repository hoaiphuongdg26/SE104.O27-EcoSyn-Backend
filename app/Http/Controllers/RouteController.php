<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Route;
use App\Http\Resources\RouteResource;

class RouteController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Route::class);
        $routes = Route::all();
        return RouteResource::collection($routes);
    }
    public function store(Request $request)
    {
        $this->authorize('create', Route::class);

        $validatedData = $request->validate([
            'route_id' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $route = Route::create([
            'route_id' => data_get($validatedData, 'route_id'),
            'start_time' => data_get($validatedData, 'start_time'),
            'end_time' => data_get($validatedData, 'end_time'),
        ]);

        return new RouteResource($route);
    }

    public function show(string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('view', $route);
        return new RouteResource($route);
    }
    public function update(Request $request, string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('update', $route);

        $validatedData = $request->validate([
            'route_id' => 'sometimes|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date',
        ]);

        $route->update($validatedData);
        return response()->json(null, 204);
    }

    public function destroy(string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('delete', $route);
        $route->delete();
        return response()->json(null, 204);
    }
}
