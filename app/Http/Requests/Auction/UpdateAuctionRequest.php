<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Auction;
use App\Models\Meeting;
use Carbon\Carbon;

class UpdateAuctionRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => 'required|date|after:end_at',
            'input_bid' => 'required|integer|min:1|max:' . Auction::PRICE_MAX_VALUE,
            'minimal_step' => 'required|integer|min:1|max:' . Auction::PRICE_MAX_VALUE,
            'min_age' => 'required|integer|min:0|max:99|lte:max_age',
            'max_age' => 'required|integer|min:0|max:99|gte:min_age',
            'description' => 'nullable|string|max:120',
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            'charity_organization_id' => 'nullable|integer|exists:charity_organizations,id',
            'photo_verified_only' => 'required|boolean',
            'fully_verified_only' => 'required|boolean',
            'location_for_winner_only' => 'required|boolean',
            'end_at' => 'required|date|after:'. Carbon::now()->addHour() .'|before:meeting_date|before_or_equal:' . Carbon::now()->addYear()
        ];
    }
}
