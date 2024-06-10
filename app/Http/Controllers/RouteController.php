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
            'start_home' => 'required|exists:homes,id',
            'end_home' => 'required|exists:homes,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $route = Route::create($validatedData);

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
            'start_home' => 'sometimes|exists:homes,id',
            'end_home' => 'sometimes|exists:homes,id',
            'status_id' => 'sometimes|exists:statuses,id',
        ]);

        $route->update($validatedData);

        return new RouteResource($route);
    }

    public function destroy(string $id)
    {
        $route = Route::findOrFail($id);
        $this->authorize('delete', $route);
        $route->delete();
        return response()->json(null, 204);
    }
}
