<?php

namespace App\Http\Requests\Support;

use App\Libraries\GraphQL\AbstractValidation;

class SupportMessagesRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'support_id' => 'required|integer',
            'limit' => 'integer',
            'offset' => 'integer',

        ];
    }
}
