<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IOT_DeviceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\RouteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/token', function (Request $request) {
    $token = $request->session()->token();

    return csrf_token();

    // ...
});
Route::middleware(['auth:sanctum'])->get('/current_user', function (Request $request) {
    return new UserResource($request->user());
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('posts', PostController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::apiResource('homes', HomeController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('vehicles', VehicleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/vehicles/{vehicle}/attach-user', [VehicleController::class, 'attachUser']);
    Route::get('/users/{userId}/get-vehicles', [UserController::class, 'getVehiclesByUser']);
    Route::get('/vehicles/{vehicleId}/get-users', [VehicleController::class, 'getUsersByVehicle']);
    Route::apiResource('iot-devices', IOT_DeviceController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::apiResource('schedules', ScheduleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('/schedules/get/latest', [ScheduleController::class, 'getLatest']);
    Route::apiResource('routes', RouteController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('reports', ReportController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    
    Route::delete('/reports/delete/bulk-delete', [ReportController::class, 'bulkDelete']);
    Route::delete('/posts/delete/bulk-delete', [PostController::class, 'bulkDelete']);
    Route::delete('/users/delete/bulk-delete', [UserController::class, 'bulkDelete']);
    Route::delete('/homes/delete/bulk-delete', [HomeController::class, 'bulkDelete']);
    Route::delete('/vehicles/delete/bulk-delete', [VehicleController::class, 'bulkDelete']);
    Route::delete('/routes/delete/bulk-delete', [RouteController::class, 'bulkDelete']);
    Route::delete('/schedules/delete/bulk-delete', [ScheduleController::class, 'bulkDelete']);

});
