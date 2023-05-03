<?php

use App\Http\Controllers\API\V1\AuthBaseController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\LocationsController;
use App\Http\Controllers\API\V1\NotificationsController;
use App\Http\Controllers\API\V1\Customer\HomeController;
use App\Http\Controllers\API\V1\Customer\AddressesController;
use App\Http\Controllers\API\V1\Customer\OrdersController;
use App\Http\Controllers\API\V1\Customer\ReasonsController;
use App\Http\Controllers\API\V1\Vendor\ReasonsController as VendorReasonsController;
use App\Http\Controllers\API\V1\Customer\ReviewController;
use App\Http\Controllers\API\V1\Customer\VendorsController as CustomerVendorsController;
use App\Http\Controllers\API\V1\LayoutsController;
use App\Http\Controllers\API\V1\Vendor\AttachmentsController;
use App\Http\Controllers\API\V1\Vendor\OrdersController as VendorOrdersController;
use App\Http\Controllers\API\V1\Vendor\ReviewController as VendorReviewController;
use App\Http\Controllers\API\V1\Vendor\TracingVendorCntroller;
use App\Http\Controllers\API\V1\Vendor\VendorsController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\QueryBuilder\QueryBuilder;

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

Route::get('/test', function () {
    $users = QueryBuilder::for(User::class)
    // ->join('customers' , 'customers.user_id' , 'users.id')
    ->allowedIncludes('customer')
    // ->allowedFilters(AllowedFilter::exact('customers.name', null, false))
    ->get();
    return $users;
});

Route::prefix('V1')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('submitcode', [AuthController::class, 'submitCode']);

    Route::prefix('customer')->middleware(['auth:sanctum'])->group(function () {
        Route::get('home', [HomeController::class, 'home']);
        Route::get('settings', [HomeController::class, 'settings']);
        Route::resource('vendor', CustomerVendorsController::class);
        Route::resource('review', ReviewController::class);
        Route::resource('order', OrdersController::class);
        Route::post('reorder/{id}', [OrdersController::class, 'reorder']);
        Route::resource('address', AddressesController::class);
        Route::put('update/profile', [AuthController::class, 'updateInfo']);
        Route::get('reason', ReasonsController::class);
    });

    Route::prefix('vendor')->middleware(['auth:sanctum'])->group(function () {
        Route::resource('attachment', AttachmentsController::class);
        Route::resource('vendor', VendorsController::class);
        Route::resource('order', VendorOrdersController::class);
        Route::get('reason', VendorReasonsController::class);
        Route::get('review', VendorReviewController::class);
        Route::put('status/{id}', [VendorsController::class , 'status']);
        Route::post('location', TracingVendorCntroller::class);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('location', LocationsController::class);
        Route::get('layout', LayoutsController::class);
        Route::resource('notification', NotificationsController::class);
        Route::delete('delete/profile', [AuthController::class , 'deleteAcount']);
        Route::get('logout', [AuthBaseController::class , 'logout']);
    });
});


