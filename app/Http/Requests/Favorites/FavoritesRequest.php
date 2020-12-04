<?php

namespace App\Http\Requests\Favorites;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\SubscriberUserPublications;

class FavoritesRequest extends AbstractValidation
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
            'type' => 'array',
            'type.*' => 'string|in:' . implode(',', array_keys(SubscriberUserPublications::availableParams('pub_type'))),
            'limit' => 'integer',
            'offset' => 'integer',
        ];
    }

    public function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0
        ];
    }
}
