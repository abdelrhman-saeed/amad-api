<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightOfferRequest extends FormRequest
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
            'currencyCode' => "required|size:3",

            "originDestinations.*.id"                            => "required|numeric",
            "originDestinations.*.originLocationCode"            => "required|size:3",
            "originDestinations.*.destinationLocationCode"       => "required|size:3",
            "originDestinations.*.departureDateTimeRange.date"   => "required|date",

            "travelers.*.id"            => "required|numeric",
            "travelers.*.travelerType"  => "required|in:ADULT,CHILD",
            "travelers.*.fareOptions"   => "required|array",

            "sources" => "required|array",

            "searchCriteria" => "required|array",
            "searchCriteria.pricingOptions.fareType" => "required|array",
            "searchCriteria.pricingOptions.includedCheckedBagsOnly" => "required|boolean"
        ];
    }
}
