<?php

namespace App\Http\Requests\Admin\ProfilesBackground;

use App\Libraries\GraphQL\AbstractValidation;

class UploadProfilesBackgroundsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'required|file',
        ];
    }
}
