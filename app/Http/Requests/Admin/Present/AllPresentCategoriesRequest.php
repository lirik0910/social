<?php

namespace App\Http\Requests\Admin\Present;

use App\Libraries\GraphQL\AbstractValidation;

class AllPresentCategoriesRequest extends AbstractValidation
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
            'offset' => 'integer',
            'filter' => 'array',
            'filter.availability' => 'boolean'
        ];
    }

    /**
     * Return default values for request params
     *
     * @return array|int[]
     */
    protected function defaultValues() : array
    {
        return [
            'limit' => 20,
            'offset' => 0,
        ];
    }
}
