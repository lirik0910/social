<?php

namespace App\Http\Requests\Admin\Meeting;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;

class AllUsersMeetingsTotalRequest extends AbstractValidation
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
            'status' => 'integer|in:' . implode(',', array_keys(Meeting::availableParams('status'))),
            'buyer' => 'string',
            'seller' => 'string',
            'free' => 'boolean',
            'charity_only' => 'boolean',
            'price_period' => 'array',
            'price_period.from' => 'integer|lte:price_period.to',
            'price_period.to' => 'integer|gte:price_period.from',
            'updated_date' => 'date',
            'updated_date_period' => 'array',
            'updated_date_period.from' => 'required_with:updated_date_period|date|lte:updated_date_period.to',
            'updated_date_period.to' => 'required_with:updated_date_period|date|gte:updated_date_period.from',
        ];
    }
}
