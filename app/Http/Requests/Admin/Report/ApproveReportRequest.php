<?php

namespace App\Http\Requests\Admin\Report;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Report;

class ApproveReportRequest extends AbstractValidation
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
            'reported_id' => 'required|string',
            'reported_type' => 'required|in:' . implode(',', array_keys(Report::availableParams('type'))),
            'moderation_reason' => 'required|integer',
        ];
    }
}
