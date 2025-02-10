<?php

namespace App\Http\Controllers;

use App\Http\Requests\FightPricingRequest;
use App\Http\Requests\FlightOfferRequest;
use Illuminate\Support\Facades\Http;
use App\Models\TokenID;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\UserLog;


class FlightsController extends Controller
{
    private PersonalAccessToken $token;
    private TokenID $tokenID;
    private float $pricingPercentage = 0.5 / 100;

    public function __construct()
    {
        $this->token    = auth()->user()->currentAccessToken();
        $this->tokenID  = TokenID::where('token_id', $this->token['id'])->firstOrFail();
    }

    private function externalAPICall(string $endpoint, array $data): array
    {

        $response = Http::withToken($this->tokenID->access_token)
                        ->withHeader('ama-client-ref', $this->tokenID->uuid)
                        ->post($endpoint, $data)
                        ->json();

        UserLog::create([
            'user_id'   => auth()->user()->id,
            'token_id'  => $this->tokenID->id,
            'request'   => json_encode($data),
            'response'  => json_encode($response)
        ]);

        return $response;
    }

    private function adjustPricing(array &$price, bool $increase = true): void
    {
        $factor = $increase ? (1 + $this->pricingPercentage) : (1 - $this->pricingPercentage);

        $price['total'] = round($price['total'] * $factor, 2);
        $price['base']  = round($price['base']  * $factor, 2);

        if (isset($price['grandTotal'])) {
            $price['grandTotal'] = round($price['grandTotal'] * $factor, 2);
        }
    }

    public function offers(FlightOfferRequest $request): array
    {
        $result = $this->externalAPICall(env('AMADEUS_FLIGHT_OFFERS'), $request->validated());

        foreach ($result['data'] as &$offer) {
            $this->adjustPricing($offer['price']);

            foreach ($offer['travelerPricings'] as &$travelerPricing) {
                $this->adjustPricing($travelerPricing['price']);
            }
        }

        return $result;
    }

    public function pricing(FightPricingRequest $request): array
    {
        $validated = $request->validated();

        foreach ($validated['data']['flightOffers'] as &$offer)
        {
            $this->adjustPricing($offer['price'], false);

            foreach ($offer['travelerPricings'] as &$travelerPricing) {
                $this->adjustPricing($travelerPricing['price'], false);
            }
        }

        $result = $this->externalAPICall(
                    env('AMADEUS_FLIGHT_PRICING'),
                    $validated
                );

        foreach ($result['data']['flightOffers'] as &$offer)
        {
            $this->adjustPricing($offer['price']);
            
            foreach ($offer['travelerPricings'] as &$travelerPricing) {
                $this->adjustPricing($travelerPricing['price']);
            }
        }

        return $result;
    }
}
