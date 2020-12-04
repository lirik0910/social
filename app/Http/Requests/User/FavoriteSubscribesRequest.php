<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class FavoriteSubscribesRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'limit' => 'integer'
        ];
    }

    public function defaultValues(): array
    {
        return [
            'limit' => 10
        ];
    }
}
