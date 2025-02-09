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
        $result = $this->externalAPICall(
                    env('AMADEUS_FLIGHT_OFFERS'),
                    $request->validated()
                );

        foreach($result['data'] as &$offer)
        {
            $offer['price'] = $this->addAdditionalPricingPercentage($offer['price']);

            foreach($offer['travelerPricings'] as &$travelerPricing) {
                $travelerPricing['price'] = $this->addAdditionalPricingPercentage($travelerPricing['price']);
            }
        }

        return $result;
    }

    private function addAdditionalPricingPercentage(array $price): array
    {
        $additionalPricingPercentage = 0.5;

        return [
            'currency'  => $price['currency'],
            'total'     => $price['total'] + $price['total'] * $additionalPricingPercentage,
            'base'      => $price['base']  + $price['base']  * $additionalPricingPercentage,
        ];
    }

    private function restorePricing(array $price): array
    {
        $additionalPricingPercentage = 0.5;

        return [
            'currency'  => $price['currency'],
            'total'     => $price['total'] - $price['total'] * $additionalPricingPercentage,
            'base'      => $price['base']  - $price['base']  * $additionalPricingPercentage,
        ];
    }

    public function pricing(FightPricingRequest $request): array
    {
        $validated = $request->validated();

        foreach($validated['data']['flightOffers'] as &$offer)
        {
            $offer['price'] = $this->restorePricing($offer['price']);

            foreach($offer['travelerPricings'] as &$travelerPricing) {
                $travelerPricing['price'] = $this->restorePricing($travelerPricing['price']);
            }
        }

        $result = $this->externalAPICall(
                    env('AMADEUS_FLIGHT_PRICING'),
                    $validated
                );

        foreach($result['data']['flightOffers'] as &$offer)
        {
            $offer['price'] = $this->addAdditionalPricingPercentage($offer['price']);

            foreach($offer['travelerPricings'] as &$travelerPricing) {
                $travelerPricing['price'] = $this->addAdditionalPricingPercentage($travelerPricing['price']);
            }
        }

        return $result;
    }
}
