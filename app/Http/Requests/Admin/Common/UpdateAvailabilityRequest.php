<?php

namespace App\Http\Requests\Admin\Common;

use App\Libraries\GraphQL\AbstractValidation;


class UpdateAvailabilityRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
            'available' => 'required|boolean'
        ];
    }
}
