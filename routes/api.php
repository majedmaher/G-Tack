<?php

use App\Http\Controllers\API\V1\AddressesController;
use App\Http\Controllers\API\V1\AuthBaseController;
use App\Http\Controllers\API\V1\HomeController;
use App\Http\Controllers\API\V1\OrdersController;
use App\Http\Controllers\API\V1\ReviewController;
use App\Http\Controllers\API\V1\UserApiAuthController;
use App\Http\Controllers\API\V1\VendersController;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('')->middleware(['auth:sanctum'])->group(function () {
    Route::get('home', [HomeController::class, 'home']);
    Route::get('settings', [HomeController::class, 'settings']);
    Route::resource('vender', VendersController::class);
    Route::resource('review', ReviewController::class);
    Route::resource('order', OrdersController::class);
    Route::resource('address', AddressesController::class);
    Route::put('update/profile', [UserApiAuthController::class, 'updateInfo']);
    Route::delete('delete/profile', [UserApiAuthController::class , 'deleteAcount']);
    Route::get('logout', [AuthBaseController::class , 'logout']);
});


Route::prefix('')->namespace('API')->group(function () {
    Route::post('register', [UserApiAuthController::class, 'register']);
    Route::post('login', [UserApiAuthController::class, 'login']);
    Route::post('submitcode', [UserApiAuthController::class, 'submitCode']);
});
