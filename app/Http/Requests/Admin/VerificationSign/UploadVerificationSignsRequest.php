<?php

namespace App\Http\Requests\Admin\VerificationSign;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class UploadVerificationSignsRequest extends AbstractValidation
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
