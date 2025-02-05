<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::prefix('amadeus')
//         ->group(function ()
//             {
//                Route::get('authenticate', function ()
//                 {
//                     $result = Http::withHeaders(['ama-client-ref' => Str::uuid()->__tostring()])
//                                         ->asForm()
//                                         ->post(env('AMADEUS_AUTH'), [
//                                             'grant_type'    => env('AMADEUS_GRANT_TYPE'),
//                                             'client_id'     => env('AMADEUS_CLIENT_ID'),
//                                             'client_secret' => env('AMADEUS_CLIENT_SECRET'),

//                                         ])->json();

//                     print_r($result);
//                 });
//             });