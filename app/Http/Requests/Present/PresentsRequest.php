<?php

namespace App\Http\Requests\Present;

use App\Libraries\GraphQL\AbstractValidation;

class PresentsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'category_id' => 'required|integer',
            'limit' => 'integer',
            'offset' => 'integer'
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
        ];
    }
}
