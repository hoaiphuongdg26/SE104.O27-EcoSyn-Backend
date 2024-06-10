<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);
    
        $routeId = DB::table('routes')->insertGetId([
            'route_id' => $validatedData['route_id'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response()->json(['route_id' => $routeId], 201);
    }

    public function show(string $id)
    {
        $route = DB::table('routes')
            ->select('route_id', 'start_time', 'end_time', 'created_at', 'updated_at')
            ->where('id', $id)
            ->first();
        if (!$route) {
            return response()->json(['message' => 'Route not found'], 404);
        }
        return response()->json($route);
    }
    public function update(Request $request, string $id)
    {
         $validatedData = $request->validate([
            'route_id' => 'exists:routes,id',
            'start_time' => 'date',
            'end_time' => 'date',
        ]);

        DB::table('routes')
            ->where('id', $id)
            ->update([
                'route_id' => $validatedData['route_id'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'updated_at' => now(),
            ]);

        return response()->json(null, 204);
    }

    public function destroy(string $id)
    {
         DB::table('routes')->where('id', $id)->delete();
         return response()->json(null, 204);
    }
}
