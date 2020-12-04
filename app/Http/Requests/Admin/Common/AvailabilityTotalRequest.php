<?php

namespace App\Http\Requests\Admin\Common;

use App\Libraries\GraphQL\AbstractValidation;

class AvailabilityTotalRequest extends AbstractValidation
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'available' => 'boolean'
        ];
    }
}
