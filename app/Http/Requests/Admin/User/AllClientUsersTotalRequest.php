<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Profile;


class AllClientUsersTotalRequest extends AbstractValidation
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
            'nickname' => 'string',
            'sex' => 'integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
            'country' => 'string',
            'banned' => 'boolean',
            'filter.age' => 'array',
            'filter.age.from' => 'required_with:filter.age|integer|min:0|max:99|lte:filter.age.to',
            'filter.age.to' => 'required_with:filter.age|integer|min:0|max:99|gte:filter.age.from',
            'created_date' => 'date',
            'created_date_period' => 'array',
            'created_date_period.from' => 'required_with:created_date_period|date|lte:created_date_period.to',
            'created_date_period.to' => 'required_with:created_date_period|date|gte:created_date_period.from',
        ];
    }
}
