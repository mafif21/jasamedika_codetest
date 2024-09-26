<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\RentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/cars', CarController::class);

Route::prefix('rents')->group(function () {
  Route::get('/', [RentController::class, 'index']);
  Route::post('/', [RentController::class, 'store']);
  Route::get('/{id}', [RentController::class, 'show']);
});