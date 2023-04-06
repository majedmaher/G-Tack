<?php

use App\Http\Controllers\API\AuthBaseController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\UserApiAuthController;
use App\Http\Controllers\API\VendersController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('')->middleware(['auth:sanctum'])->group(function () {
    Route::get('home', [HomeController::class, 'home']);
    Route::get('settings', [HomeController::class, 'settings']);
    Route::resource('vender', VendersController::class);
    Route::put('update/profile', [UserApiAuthController::class, 'updateInfo']);
    Route::delete('delete/profile', [UserApiAuthController::class , 'deleteAcount']);
    Route::get('logout', [AuthBaseController::class , 'logout']);

});


Route::prefix('')->namespace('API')->group(function () {
    Route::post('register', [UserApiAuthController::class, 'register']);
    Route::post('login', [UserApiAuthController::class, 'login']);
    Route::post('submitcode', [UserApiAuthController::class, 'submitCode']);
});
