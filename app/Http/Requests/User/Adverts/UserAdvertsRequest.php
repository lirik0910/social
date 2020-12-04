<?php

namespace App\Http\Requests\User\Adverts;

use App\GraphQL\Queries\Advert\UserAdverts;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Advert;

class UserAdvertsRequest extends AbstractValidation
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
            'filter' => 'array',
            'filter.type' => 'integer|in:' . implode(',', array_keys(Advert::availableParams('type'))),
            'filter.status' => 'integer|in:' . implode(',', array_keys(UserAdverts::availableParams('status')))
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0
        ];
    }
}
