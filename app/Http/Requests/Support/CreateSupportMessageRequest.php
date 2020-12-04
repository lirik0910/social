<?php

namespace App\Http\Requests\Support;

use App\Libraries\GraphQL\AbstractValidation;

class CreateSupportMessageRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'support_id' => 'required|string',
            'message' => 'required|string|min:1|max:500'
        ];
    }
}
