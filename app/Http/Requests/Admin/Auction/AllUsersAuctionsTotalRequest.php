<?php

namespace App\Http\Requests\Admin\Auction;

use App\Libraries\GraphQL\AbstractValidation;

class AllUsersAuctionsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'verified_only' => 'boolean',
            'charity_only' => 'boolean',
            'user' => 'string',
            'created_date' => 'date',
            'created_date_period' => 'array',
            'created_date_period.from' => 'required_with:created_date_period|date|lte:created_date_period.to',
            'created_date_period.to' => 'required_with:created_date_period|date|gte:created_date_period.from',
        ];
    }
}
