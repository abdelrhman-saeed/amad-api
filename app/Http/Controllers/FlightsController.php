<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightOfferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TokenID;


class FlightsController extends Controller
{
    public function offers(FlightOfferRequest $request): array
    {
        $token = auth()->user()->currentAccessToken();
        $externalToken = TokenID::where('token_id', $token['id'])->firstOrFail();

        return Http::withToken($externalToken->access_token)
                    ->withHeader('ama-client-ref', $externalToken->uuid)
                    ->post(env('AMADEUS_FLIGHT_OFFERS'), $request->validated())
                    ->json();
    }

    public function pricing(): string
    {
        return '';
    }
}
