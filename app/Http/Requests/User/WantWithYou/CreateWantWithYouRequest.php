<?php

namespace App\Http\Requests\User\WantWithYou;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\WantWithYou;

class CreateWantWithYouRequest extends AbstractValidation
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
            'user_id' => 'required|string',
            'type' => 'required|integer|in:' . implode(',', array_keys(WantWithYou::availableParams('type')))
        ];
    }
}
