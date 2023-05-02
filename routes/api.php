<?php

use App\Http\Controllers\API\V1\AuthBaseController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\LocationsController;
use App\Http\Controllers\API\V1\NotificationsController;
use App\Http\Controllers\API\V1\Customer\HomeController;
use App\Http\Controllers\API\V1\Customer\AddressesController;
use App\Http\Controllers\API\V1\Customer\OrdersController;
use App\Http\Controllers\API\V1\Customer\ReasonsController;
use App\Http\Controllers\API\V1\Vender\ReasonsController as VenderReasonsController;
use App\Http\Controllers\API\V1\Customer\ReviewController;
use App\Http\Controllers\API\V1\Customer\UserApiAuthController;
use App\Http\Controllers\API\V1\Customer\VendorsController as CustomerVendorsController;
use App\Http\Controllers\API\V1\LayoutsController;
use App\Http\Controllers\API\V1\Vender\AttachmentsController;
use App\Http\Controllers\API\V1\Vender\OrdersController as VenderOrdersController;
use App\Http\Controllers\API\V1\Vender\ReviewController as VenderReviewController;
use App\Http\Controllers\API\V1\Vender\VendorsController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

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

Route::get('/user', function () {
    $users = QueryBuilder::for(User::class)
    // ->join('customers' , 'customers.user_id' , 'users.id')
    ->allowedIncludes('custmer')
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
        Route::resource('vender', CustomerVendorsController::class);
        Route::resource('review', ReviewController::class);
        Route::resource('order', OrdersController::class);
        Route::post('reorder/{id}', [OrdersController::class, 'reorder']);
        Route::resource('address', AddressesController::class);
        Route::put('update/profile', [UserApiAuthController::class, 'updateInfo']);
        Route::delete('delete/profile', [UserApiAuthController::class , 'deleteAcount']);
        Route::get('reason', ReasonsController::class);
        Route::get('logout', [AuthBaseController::class , 'logout']);
    });

    Route::prefix('vender')->middleware(['auth:sanctum'])->group(function () {
        Route::resource('attachment', AttachmentsController::class);
        Route::resource('vender', VendorsController::class);
        Route::resource('order', VenderOrdersController::class);
        Route::get('reason', VenderReasonsController::class);
        Route::get('review', VenderReviewController::class);
        Route::put('status/{id}', [VendorsController::class , 'status']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('location', LocationsController::class);
        Route::get('layout', LayoutsController::class);
        Route::resource('notification', NotificationsController::class);
    });
});


