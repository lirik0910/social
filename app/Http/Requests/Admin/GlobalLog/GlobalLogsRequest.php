<?php

namespace App\Http\Requests\Admin\GlobalLog;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\GlobalLog;

class GlobalLogsRequest extends AbstractValidation
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
            'limit' => 'integer',
            'offset' => 'integer',
            'order_by_dir' => 'in:DESC,ASC',
            'filter' => 'array',
            'filter.mutation' => 'integer',
            'filter.section' => 'integer|in:' .  implode(',', array_keys(GlobalLog::availableParams('admin_section'))),
            'filter.user_nickname' => 'string',
            'filter.user_id' => 'string',
            'filter.created_date' => 'date',
            'filter.created_date_period' => 'array',
            'filter.created_date_period.from' => 'required_with:filter.created_date_period|date|lte:filter.created_date_period.to',
            'filter.created_date_period.to' => 'required_with:filter.created_date_period|date|gte:filter.created_date_period.from',
        ];
    }

    /**
     * @return array|int[]
     */
    protected function defaultValues() : array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'order_by_dir' => 'DESC'
        ];
    }
}
