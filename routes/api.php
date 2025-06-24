<?php

use App\Http\Controllers\ConfigModule\Boarding\BoardingController;
use App\Http\Controllers\ConfigModule\Fare\FareController;
use App\Http\Controllers\ConfigModule\Layouts\StandardLayout\StandardLayoutController;
use App\Http\Controllers\RouteModule\Location\LocationController;
use App\Http\Controllers\RouteModule\ServingRoutes\ServingRouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// admin api's starts here

Route::post('/location', [LocationController::class, 'resource']);
Route::post('/layout', [StandardLayoutController::class, 'resource']); // standard || custom
Route::post('/serving-route', [ServingRouteController::class, 'resource']);
Route::post('/boarding', [BoardingController::class, 'resource']);
Route::post('/fair', [FareController::class, 'resource']);

// admin api's ends here