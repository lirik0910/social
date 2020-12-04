<?php

namespace App\Http\Requests\Admin\Report;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Report;


class AllReportsTotalRequest extends AbstractValidation
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
            'type' => 'in:' . implode(',', array_keys(Report::availableParams('type'))),
            'filter' => 'array',
            'filter.reported_user' => 'string|max:18',
            'filter.status' => 'integer|in:' . implode(',', array_keys(Report::availableParams('status'))),
            'filter.reason' => 'integer',
        ];
    }
}
