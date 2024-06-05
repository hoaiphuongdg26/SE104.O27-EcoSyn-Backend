<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IOT_DeviceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/iot_device', [IOT_DeviceController::class, 'store']);
Route::get('/iot_devices', [IOT_DeviceController::class, 'index']);
Route::get('/iot_devices/{id}', [IOT_DeviceController::class, 'detail']);


