<?php

namespace App\Http\Requests\WantWithYou;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\WantWithYou;

class CreateWantWithYouRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'user_id' => 'required|integer',
            'type' => 'required|integer|in:' . implode(',', array_keys(WantWithYou::availableParams('type')))
        ];
    }
}
