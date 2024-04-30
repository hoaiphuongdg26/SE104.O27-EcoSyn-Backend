<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\IOT_DeviceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/iot_devices', [IOT_DeviceController::class, 'store']);
Route::get('/iot_devices', [IOT_DeviceController::class, 'index']);
Route::get('/iot_devices/{id}', [IOT_DeviceController::class, 'detail']);

