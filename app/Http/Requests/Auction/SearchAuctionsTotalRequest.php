<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;
use App\Models\Profile;

class SearchAuctionsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'filter' => 'array',
            'filter.text' => 'nullable|string|max:12',
            'filter.region' => 'nullable|string',
            'filter.country' => 'nullable|string',
            'filter.age' => 'required_with:filter|array',
            'filter.age.from' => 'required_with:filter.age|integer|min:'. Profile::MIN_AVAILABLE_AGE .'|max:99|lte:filter.age.to',
            'filter.age.to' => 'required_with:filter.age|integer|min:'. Profile::MIN_AVAILABLE_AGE .'|max:99|gte:filter.age.from',
            'filter.sex' => 'integer|in:'  . implode(',', array_keys(Profile::availableParams('gender'))),
            'filter.physique' => 'integer|in:'  . implode(',', array_keys(Profile::availableParams('physique'))),
            'filter.eye' => 'integer|in:'  . implode(',', array_keys(Profile::availableParams('eye_color'))),
            'filter.hair' => 'integer|in:'  . implode(',', array_keys(Profile::availableParams('hair_color'))),
            'filter.minimal_step' => 'required_with:filter|array',
            'filter.minimal_step.from' => 'required_with:filter.minimal_step|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|lte:filter.minimal_step.to',
            'filter.minimal_step.to' => 'required_with:filter.minimal_step|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|gte:filter.minimal_step.from',
            'filter.latest_bid' => 'required_with:filter|array',
            'filter.latest_bid.from' => 'required_with:filter.latest_bid|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|lte:filter.latest_bid.to',
            'filter.latest_bid.to' => 'required_with:filter.latest_bid|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|gte:filter.latest_bid.from',
            'filter.charity_only' => 'boolean',
            'filter.photo_verified_only' => 'boolean',
            'filter.end_soon_only' => 'boolean',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'filter' => []
        ];
    }
}
