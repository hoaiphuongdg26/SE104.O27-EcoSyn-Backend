<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);
    
        // Thêm route vào cơ sở dữ liệu
        $routeId = DB::table('routes')->insertGetId([
            'route_id' => $validatedData['route_id'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Trả về response thành công
        return response()->json(['route_id' => $routeId], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Lấy thông tin của route từ cơ sở dữ liệu
        $route = DB::table('routes')
            ->select('route_id', 'start_time', 'end_time', 'created_at', 'updated_at')
            ->where('id', $id)
            ->first();

        // Kiểm tra xem route có tồn tại không
        if (!$route) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        // Trả về thông tin của route
        return response()->json($route);
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
         // Validation
         $validatedData = $request->validate([
            'route_id' => 'exists:routes,id',
            'start_time' => 'date',
            'end_time' => 'date',
        ]);

        // Cập nhật route trong cơ sở dữ liệu
        DB::table('routes')
            ->where('id', $id)
            ->update([
                'route_id' => $validatedData['route_id'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'updated_at' => now(),
            ]);

        // Trả về response thành công
        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         // Xóa route khỏi cơ sở dữ liệu
         DB::table('routes')->where('id', $id)->delete();

         // Trả về response thành công
         return response()->json(null, 204);
    }
}
