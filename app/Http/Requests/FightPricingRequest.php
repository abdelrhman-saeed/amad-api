<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FightPricingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "data"      => "required|array",
            "data.type" => "required|string",

            // offers
            "data.flightOffers" => "required|array",
            "data.flightOffers.*.type"                        => "required|string",
            "data.flightOffers.*.id"                          => "required|numeric",
            "data.flightOffers.*.source"                      => "required|string|size:3",
            "data.flightOffers.*.instantTicketingRequired"    => "required|boolean",
            "data.flightOffers.*.nonHomogeneous"              => "required|boolean",
            "data.flightOffers.*.oneWay"                      => "required|boolean",
            "data.flightOffers.*.isUpsellOffer"               => "required|boolean",
            "data.flightOffers.*.lastTicketingDate"           => "required|date",
            "data.flightOffers.*.lastTicketingDateTime"       => "required|date",
            "data.flightOffers.*.numberOfBookableSeats"       => "required|numeric",

            // itineraries
            "data.flightOffers.*.itineraries"               => "required|array",
            "data.flightOffers.*.itineraries.*.duration"    => "required|string",
            "data.flightOffers.*.itineraries.*.segments"    => "required|array",

            "data.flightOffers.*.itineraries.*.segments.*.departure" => "required|array",

            "data.flightOffers.*.itineraries.*.segments.*.departure.iataCode" => "string|size:3",
            "data.flightOffers.*.itineraries.*.segments.*.departure.terminal" => "numeric",
            "data.flightOffers.*.itineraries.*.segments.*.departure.at"       => "date_format:Y-m-d\TH:i:s",

            "data.flightOffers.*.itineraries.*.segments.*.arrival" => "array",

            "data.flightOffers.*.itineraries.*.segments.*.arrival.iataCode" => "string|size:3",
            "data.flightOffers.*.itineraries.*.segments.*.arrival.terminal" => "numeric",
            "data.flightOffers.*.itineraries.*.segments.*.arrival.at"       => "date_format:Y-m-d\TH:i:s",

            "data.flightOffers.*.itineraries.*.segments.*.carrierCode"    => "string",
            "data.flightOffers.*.itineraries.*.segments.*.number"         => "numeric",

            "data.flightOffers.*.itineraries.*.segments.*.aircraft.code"  => "string",

            "data.flightOffers.*.itineraries.*.segments.*.operating.carrierCode" => "string",

            "data.flightOffers.*.itineraries.*.segments.*.duration"           => "string",
            "data.flightOffers.*.itineraries.*.segments.*.id"                 => "numeric",
            "data.flightOffers.*.itineraries.*.segments.*.numberOfStops"      => "numeric",
            "data.flightOffers.*.itineraries.*.segments.*.blacklistedInEU"    => "bool",

            // pricing
            "data.flightOffers.*.price.currency"  => "required|size:3",
            "data.flightOffers.*.price.total"     => "required|numeric",
            "data.flightOffers.*.price.base"      => "required|numeric",

            "data.flightOffers.*.price.fees"          => "required|array",
            "data.flightOffers.*.price.fees.*.amount" => "required|numeric",
            "data.flightOffers.*.price.fees.*.type"   => "required|string",
            "data.flightOffers.*.price.grandTotal"    => "required|numeric",

            // pricing options
            "data.flightOffers.*.pricingOptions"                => "required|array",
            "data.flightOffers.*.pricingOptions.fareType"       => "required|array",
            "data.flightOffers.*.pricingOptions.includedCheckedBagsOnly" => "required|boolean",

            //  air line codes.* validation
            "data.flightOffers.*.validatingAirlineCodes" => "required|array",

            // traveler pricin.*gs
            "data.flightOffers.*.travelerPricings" => "required|array",
            "data.flightOffers.*.travelerPricings.*.travelerId" => "required|numeric",
            "data.flightOffers.*.travelerPricings.*.fareOption" => "required|string",
            "data.flightOffers.*.travelerPricings.*.travelerType" => "required|in:ADULT,CHILD",

            "data.flightOffers.*.travelerPricings.*.price.currency" => "required|size:3",
            "data.flightOffers.*.travelerPricings.*.price.total"    => "required|numeric",
            "data.flightOffers.*.travelerPricings.*.price.base"     => "required|numeric",

            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment" => "required|array",
            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment.*.segmentId"   => "required|numeric",
            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment.*.cabin"       => "required|string",
            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment.*.fareBasis"   => "required|string",
            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment.*.class"       => "required|string",
            "data.flightOffers.*.travelerPricings.*.fareDetailsBySegment.*.includedCheckedBags.quantity" => "numeric",
        ];
    }
}
