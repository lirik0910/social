<?php

namespace App\Http\Requests\Admin\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;

class ChangeSupportStatusRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'status' => 'required|integer|in:' . implode(',', [Support::STATUS_IN_PROGRESS, Support::STATUS_CLOSED])
        ];
    }
}
