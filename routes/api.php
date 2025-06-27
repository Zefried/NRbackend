<?php

use App\Http\Controllers\AuthModule\SimpleAuth\User\UserAuthController;
use App\Http\Controllers\BookingModule\BookingLayout\GenerateLayout\GenerateLayoutController;
use App\Http\Controllers\BookingModule\PNR\PnrController;
use App\Http\Controllers\BookingModule\SeatHold\SeatHoldController;
use App\Http\Controllers\BookingModule\Tickets\TicketController;
use App\Http\Controllers\ConfigModule\Boarding\BoardingController;
use App\Http\Controllers\ConfigModule\Bus\BusController;
use App\Http\Controllers\ConfigModule\Company\CompanyController;
use App\Http\Controllers\ConfigModule\Fare\FareController;
use App\Http\Controllers\ConfigModule\Layouts\StandardLayout\StandardLayoutController;
use App\Http\Controllers\RouteModule\Location\LocationController;
use App\Http\Controllers\RouteModule\ServingRoutes\ServingRouteController;
use App\Http\Controllers\SearchModule\ClientSearchController;
use App\Http\Controllers\SearchModule\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// admin api's starts here

Route::post('/user-register', [UserAuthController::class, 'userRegister']);
Route::post('/user-login', [UserAuthController::class, 'userLogin']);

Route::post('/add-bus', [BusController::class, 'addBus']);
Route::post('/add-company-info', [CompanyController::class, 'addCompanyInfo']);

Route::post('/location', [LocationController::class, 'resource']);
Route::post('/layout', [StandardLayoutController::class, 'resource']); // standard || custom
Route::post('/generate-layout', [GenerateLayoutController::class, 'resource']);

Route::post('/serving-route', [ServingRouteController::class, 'resource']);
Route::post('/boarding', [BoardingController::class, 'resource']);
Route::post('/fair', [FareController::class, 'resource']);
Route::post('/seat-hold-config', [SeatHoldController::class, 'resource']);

Route::post('/pnr', [PnrController::class, 'resource']);
Route::post('/ticket', [TicketController::class, 'resource']);

// admin api's ends here


// Api for client are here as well
    Route::post('/location-search', [LocationController::class, 'search']);
    Route::post('/search-bus', [SearchController::class, 'searchBus']);
    Route::post('/fetch-seat-ui', [ClientSearchController::class, 'fetchSeatUI']);
    Route::post('/fetch-seat-data', [ClientSearchController::class, 'fetchSeatData']);
    Route::post('/get-boarding-detail', [ClientSearchController::class, 'fetchBoardingDetail']);
    Route::post('/get-user-seat-holdings', [ClientSearchController::class, 'getUserSeatHoldings']);
    Route::post('/get-user-pnr-data', [ClientSearchController::class, 'getuserPnr']);
    Route::post('/generate-user-ticket', [ClientSearchController::class, 'generateUserTicket']);
    Route::post('/get-user-tickets', [ClientSearchController::class, 'getUserTickets']);



// Api for client ends here