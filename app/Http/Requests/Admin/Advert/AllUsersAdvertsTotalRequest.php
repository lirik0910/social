<?php

namespace App\Http\Requests\Admin\Advert;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Advert;

class AllUsersAdvertsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'type' => 'string|in:' . implode(',', array_keys(Advert::availableParams('type'))),
            'user' => 'string',
            'free' => 'boolean',
            'charity_only' => 'boolean',
            'price_period' => 'array',
            'price_period.from' => 'integer|lte:price_period.to',
            'price_period.to' => 'integer|gte:price_period.from',
            'created_date' => 'date',
            'created_date_period' => 'array',
            'created_date_period.from' => 'required_with:created_date_period|date|lte:created_date_period.to',
            'created_date_period.to' => 'required_with:created_date_period|date|gte:created_date_period.from',
        ];
    }
}
