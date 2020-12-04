<?php

namespace App\Http\Requests\User\Adverts;

use App\GraphQL\Queries\Advert\UserAdverts;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Advert;

class UserAdvertsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules(): array
    {
        return [
            'type' => 'integer|in:' . implode(',', array_keys(Advert::availableParams('type'))),
            'status' => 'integer|in:' . implode(',', array_keys(UserAdverts::availableParams('status')))
        ];
    }
}
