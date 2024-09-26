<?php

use App\Http\Controllers\CarController;
use \App\Http\Controllers\RentController;
use \App\Http\Controllers\ReturnRentController;
use Illuminate\Http\Request;
use \App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\AuthMiddleware;

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
//Route::post('refresh', [AuthController::class,'refresh']);
//Route::post('logout', [AuthController::class,'logout']);


Route::group(['middleware' => ['auth.jwt']], function () {
    Route::apiResource('cars', CarController::class);

    Route::prefix('rents')->group(function () {
        Route::get('/', [RentController::class, 'index']);
        Route::post('/', [RentController::class, 'store']);
        Route::get('/{rent}', [RentController::class, 'show']);
        Route::delete('/{rent}', [RentController::class, 'destroy']);
    });

    Route::prefix('return')->group(function () {
        Route::get('/', [ReturnRentController::class, 'index']);
        Route::post('/', [ReturnRentController::class, 'store']);
        Route::get('/{rent}', [ReturnRentController::class, 'show']);
    });
});


