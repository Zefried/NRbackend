<?php

use App\Http\Controllers\Auth\AdminAuth\AdminLoginController;
use App\Http\Controllers\Auth\AdminAuth\AdminRegisterController;
use App\Http\Controllers\BusConfig\AddBus\AddBusController;
use App\Http\Controllers\BusConfig\Amenities\AmenitiesController;
use App\Http\Controllers\BusConfig\BusLocation\BusLocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/admin-register', [AdminRegisterController::class, 'adminRegister']);
Route::post('/admin-login', [AdminLoginController::class, 'adminLogin']);



// location master work is done 
Route::get('/verify-location', [BusLocationController::class, 'verifyLocation']);

Route::post('/add-location', [BusLocationController::class, 'addLocation']);

Route::get('/view-location', [BusLocationController::class, 'viewLocation']);
// ends here



// amenties master route starts here
Route::post('/add-amenity', [AmenitiesController::class, 'addAmenity']);
// ends here



// bus adding master route starts here
Route::post('/add-bus', [AddBusController::class, 'addBus']);
// ends here

