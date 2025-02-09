<?php

namespace App\Http\Controllers;

use App\Http\Requests\FightPricingRequest;
use App\Http\Requests\FlightOfferRequest;
use Illuminate\Support\Facades\Http;
use App\Models\TokenID;
use Laravel\Sanctum\PersonalAccessToken;
use Log;


class FlightsController extends Controller
{
    private PersonalAccessToken $token;
    private TokenID $tokenID;

    public function __construct()
    {
        $this->token      = auth()->user()->currentAccessToken();
        $this->tokenID    = TokenID::where('token_id', $this->token['id'])->firstOrFail();
    }

    private function externalAPICall(string $endoint, array $data)
    {
        $response = Http::withToken($this->tokenID->access_token)
                        ->withHeader('ama-client-ref', $this->tokenID->uuid)
                        ->post($endoint, $data)
                        ->json();

        Log::channel('request')->info("User Api Call", [
                'User'              => auth()->user()->id,
                'ama_client_ref'    => $this->tokenID->uuid,
                'endpoint'          => $endoint
            ]);

        return $response;
    }

    public function offers(FlightOfferRequest $request): array
    {
        return $this->externalAPICall(
                env('AMADEUS_FLIGHT_OFFERS'),
                $request->validated()
            );

    }

    public function pricing(FightPricingRequest $request): array
    {
        $result = $this->externalAPICall(
                env('AMADEUS_FLIGHT_PRICING'),
                $request->validated()
            );

        if (!isset($result['data']['flightOffers'])) {
            return $result;
        }

        $additionalPricingAmount = 0;

        $additionalPricingAmount = $request->filled('data.additionalPricingAmount')
            ? $request->post('data')['additionalPricingAmount']
            : 0;

        $adjustPrice = function (array $prices) use ($additionalPricingAmount) : array {
            return [
                'currency'  => $prices['currency'],
                'total'     => $prices['total'] + $prices['total'] * $additionalPricingAmount,
                'base'      => $prices['base']  + $prices['base'] * $additionalPricingAmount,
            ];
        };

        foreach($result['data']['flightOffers'] as &$offer)
        {
            if (isset($offer['price'])) {
                $offer['price'] = $adjustPrice($offer['price']);
            }

            if (isset($offer['travelerPricings'])) {

                $offer['travelerPricings']
                    = array_map(
                        fn (array $arr) => $adjustPrice($arr['price']),
                        $offer['travelerPricings']
                    );
            }
        }

        return $result;
    }
}
