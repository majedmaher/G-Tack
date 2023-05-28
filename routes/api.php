<?php

use App\Http\Controllers\API\V1\AuthBaseController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\ComplaintsController;
use App\Http\Controllers\API\V1\LocationsController;
use App\Http\Controllers\API\V1\NotificationsController;
use App\Http\Controllers\API\V1\Customer\HomeController;
use App\Http\Controllers\API\V1\Customer\AddressesController;
use App\Http\Controllers\API\V1\Customer\OrdersController;
use App\Http\Controllers\API\V1\Customer\ReasonsController;
use App\Http\Controllers\API\V1\Vendor\ReasonsController as VendorReasonsController;
use App\Http\Controllers\API\V1\Customer\ReviewController;
use App\Http\Controllers\API\V1\Customer\VendorsController as CustomerVendorsController;
use App\Http\Controllers\API\V1\Dashboard\AttachmentsController as DashboardAttachmentsController;
use App\Http\Controllers\API\V1\Dashboard\CustomerOrdersController;
use App\Http\Controllers\API\V1\Dashboard\CustomerReviewsController;
use App\Http\Controllers\API\V1\Dashboard\CustomersController;
use App\Http\Controllers\API\V1\Dashboard\DatabasesController;
use App\Http\Controllers\API\V1\Dashboard\HomeController as DashboardHomeController;
use App\Http\Controllers\API\V1\Dashboard\LayoutsController as DashboardLayoutsController;
use App\Http\Controllers\API\V1\Dashboard\LocationsController as DashboardLocationsController;
use App\Http\Controllers\API\V1\Dashboard\OrdersController as DashboardOrdersController;
use App\Http\Controllers\API\V1\Dashboard\ProductsController;
use App\Http\Controllers\API\V1\Dashboard\ReportsController;
use App\Http\Controllers\API\V1\Dashboard\UsersController;
use App\Http\Controllers\API\V1\Dashboard\VendorOrdersController as DashboardVendorOrdersController;
use App\Http\Controllers\API\V1\Dashboard\VendorReviewsController;
use App\Http\Controllers\API\V1\Dashboard\VendorsController as DashboardVendorsController;
use App\Http\Controllers\API\V1\LayoutsController;
use App\Http\Controllers\API\V1\Vendor\AttachmentsController;
use App\Http\Controllers\API\V1\Vendor\OrdersController as VendorOrdersController;
use App\Http\Controllers\API\V1\Vendor\ReviewController as VendorReviewController;
use App\Http\Controllers\API\V1\Vendor\TracingVendorCntroller;
use App\Http\Controllers\API\V1\Vendor\VendorsController;
use App\Http\Controllers\API\V1\Dashboard\SettingsController;
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
    Route::get('settings', [HomeController::class, 'settings']);
    Route::get('location', LocationsController::class);

    Route::prefix('customer')->middleware(['auth:sanctum'])->group(function () {
        Route::get('home', [HomeController::class, 'home']);
        Route::resource('vendor', CustomerVendorsController::class);
        Route::resource('review', ReviewController::class);
        Route::get('rate-customer', [ReviewController::class , 'rateCustomer']);
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

    Route::prefix('dashboard')->middleware(['auth:sanctum'])->group(function () {
        Route::resource('order', DashboardOrdersController::class);
        Route::resource('vendor', DashboardVendorsController::class);
        Route::get('vendororders/{id}', DashboardVendorOrdersController::class);
        Route::get('vendorreviews/{id}', VendorReviewsController::class);
        Route::resource('customer', CustomersController::class);
        Route::get('customerorders/{id}', CustomerOrdersController::class);
        Route::get('customerreviews/{id}', CustomerReviewsController::class);
        Route::get('home', DashboardHomeController::class);
        Route::resource('user', UsersController::class);
        Route::resource('product', ProductsController::class);
        Route::resource('location', DashboardLocationsController::class);
        Route::resource('layout', DashboardLayoutsController::class);
        Route::apiResource('attachment', DashboardAttachmentsController::class);
        Route::apiResource('setting', SettingsController::class);
        Route::post('backup', [DatabasesController::class , 'backup']);
        Route::post('restore' , [DatabasesController::class , 'restore']);
        Route::post('empty', [DatabasesController::class , 'empty']);
        Route::get('report', ReportsController::class);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::resource('complaint', ComplaintsController::class);
        Route::get('layout', LayoutsController::class);
        Route::resource('notification', NotificationsController::class);
        Route::delete('delete/profile', [AuthController::class , 'deleteAcount']);
        Route::get('logout', [AuthBaseController::class , 'logout']);
    });
});


