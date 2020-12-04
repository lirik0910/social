<?php

namespace App\Http\Requests\Meetings;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;

class MeetingsOptionRequest extends AbstractValidation
{
    protected $dataLocation = 'data';

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'minimal_price' => 'integer|min:0|max:' . Meeting::PRICE_MAX_VALUE,
            'min_age' => 'integer|min:0|max:99|lte:max_age',
            'max_age' => 'integer|min:0|max:99|gte:min_age',
            'safe_deal_only' => 'boolean',
            'photo_verified_only' => 'boolean',
            'fully_verified_only' => 'boolean',
            'charity_organization_id' => 'nullable|integer',
        ];
    }
}
