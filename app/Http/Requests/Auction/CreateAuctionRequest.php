<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Auction;
use App\Models\Meeting;
use Carbon\Carbon;

class CreateAuctionRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => 'required|date|after:end_at',
            'input_bid' => 'required|integer|min:1|max:' . Auction::PRICE_MAX_VALUE,
            'minimal_step' => 'required|integer|min:1|max:' . Auction::PRICE_MAX_VALUE,
            'min_age' => 'required|integer|min:0|max:99|lte:max_age',
            'max_age' => 'required|integer|min:0|max:99|gte:min_age',
            'description' => [
                'required',
                'string',
                'max:120',
                function ($attribute, $value, $fail) {
                    if (empty(trim($value))) {
                        $fail(__('auction.description_is_empty'));
                    }
                }
            ],
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            'charity_organization_id' => 'nullable|string',
            'photo_verified_only' => 'required|boolean',
            'location_for_winner_only' => 'required|boolean',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'end_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::now()->addHour() > $value) {
                       $fail(__('auction.early_end'));
                    }
                },
                function ($attribute, $value, $fail) {
                    if (Carbon::now()->addYear() < $value) {
                      $fail(__('auction.lately_end'));
                    }
                }
            ],
        ];
    }
}
