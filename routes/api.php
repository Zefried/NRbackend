<?php

use App\Http\Controllers\Auth\AdminAuth\AdminLoginController;
use App\Http\Controllers\Auth\AdminAuth\AdminRegisterController;
use App\Http\Controllers\BusConfig\AddBus\AddBusController;
use App\Http\Controllers\BusConfig\AddSeats\NormalSeat_SS_Controller;
use App\Http\Controllers\BusConfig\Amenities\AmenitiesController;
use App\Http\Controllers\BusConfig\BusLocation\BusLocationController;
use App\Http\Controllers\Orders\BookingRealTime\BookingController;
use App\Http\Controllers\Orders\OrderRealTime\OrderRealTimeController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');






Route::post('/admin-register', [AdminRegisterController::class, 'adminRegister']);
Route::post('/admin-login', [AdminLoginController::class, 'adminLogin']);


Route::post('/user-register', [AdminRegisterController::class, 'userRegister']);
Route::post('/user-login', [AdminLoginController::class, 'userLogin']);

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


Route::post('/add-seat', [NormalSeat_SS_Controller::class, 'addSeat']);



Route::post('/create-order', [OrderRealTimeController::class, 'createOrder']);
Route::get('/generate-link', [OrderRealTimeController::class, 'generateLink']);
Route::post('/payment-status-callback', [OrderRealTimeController::class, 'paymentStatus']);
Route::post('/create-booking', [OrderRealTimeController::class, 'createBooking']);


Route::post('/test', [OrderRealTimeController::class, 'seatConfigRun']);


Route::post('/returnDoubleSide', [NormalSeat_SS_Controller::class, 'returnDoubleSeatSide']);




/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
////////////////// All view apis are done here///////////////////


Route::get('/findGender', [TestController::class, 'findGender']);