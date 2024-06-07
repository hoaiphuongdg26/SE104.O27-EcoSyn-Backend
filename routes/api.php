<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MigrationController;

use App\Http\Controllers\IOT_DeviceController;
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
Route::post('/migrate', [MigrationController::class, 'runMigration']);

Route::post('/iot_devices', [IOT_DeviceController::class, 'store']);
Route::get('/iot_devices', [IOT_DeviceController::class, 'index']);
Route::get('/iot_devices/{id}', [IOT_DeviceController::class, 'detail']);



Route::apiResource('v1/posts', PostController::class)->only(['index', 'store', 'show', 'update', 'destroy'])
    ->middleware('auth:sanctum');

Route::apiResource('users', UserController::class)->only(['index', 'show', 'update', 'destroy'])
    ->middleware('auth:sanctum');
