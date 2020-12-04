<?php

namespace App\Http\Requests\Admin\VerificationSign;

use App\Libraries\GraphQL\AbstractValidation;

class VerificationSignsRequest extends AbstractValidation
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
            'order_by_dir' => 'in:DESC,ASC',
            'filter' => 'array',
            'filter.available' => 'boolean'
        ];
    }

    protected function defaultValues() : array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'order_by_dir' => 'DESC',
        ];
    }
}
