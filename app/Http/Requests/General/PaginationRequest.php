<?php

namespace App\Http\Requests\General;

use App\Libraries\GraphQL\AbstractValidation;

class PaginationRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'limit' => 'integer',
            'offset' => 'integer'
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0
        ];
    }
}
