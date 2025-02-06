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
                    Route::get('signout', 'signout')
                            ->middleware('auth:sanctum');

                    Route::post('signin', 'signin');
                    Route::post('signup', 'signup')->middleware('guest:sanctum');
                });

    Route::middleware('auth:sanctum')
            ->group(function ()
                {
                    Route::post('offers', [FlightsController::class, 'offers']);
                    Route::post('pricing', [FlightsController::class, 'pricing']);
                });

});