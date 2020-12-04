<?php

namespace App\Http\Requests\Admin\GlobalLog;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\GlobalLog;


class GlobalLogsTotalRequest extends AbstractValidation
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
            'mutation' => 'integer',
            'section' => 'integer|in:' .  implode(',', array_keys(GlobalLog::availableParams('admin_section'))),
            'user_nickname' => 'string',
            'user_id' => 'string',
            'created_date' => 'date',
            'created_date_period' => 'array',
            'created_date_period.from' => 'required_with:created_date_period|date|lte:created_date_period.to',
            'created_date_period.to' => 'required_with:created_date_period|date|gte:created_date_period.from',
        ];
    }
}
