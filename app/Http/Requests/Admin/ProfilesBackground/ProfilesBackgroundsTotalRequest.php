<?php

namespace App\Http\Requests\Admin\ProfilesBackground;

use App\Libraries\GraphQL\AbstractValidation;

class ProfilesBackgroundsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'user' => 'string|max:18',
            'custom' => 'boolean',
            'available' => 'boolean',
        ];
    }
}
