<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlightsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('flights')->group(function ()
{
    Route::controller(AuthController::class)
            ->group(function (): void
                {
                    Route::get('logout', 'logout')
                            ->middleware('auth:sanctum');

                    Route::post('login', 'login');
                    Route::post('register', 'register')->middleware('guest:sanctum');
                });

    Route::middleware('auth:sanctum')
            ->group(function ()
                {
                    Route::post('flight-offers', [FlightsController::class, 'offers']);
                    Route::post('flight-pricings', [FlightsController::class, 'pricing']);
                });

});