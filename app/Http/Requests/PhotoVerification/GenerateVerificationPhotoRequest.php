<?php

namespace App\Http\Requests\PhotoVerification;

use App\Libraries\GraphQL\AbstractValidation;

class GenerateVerificationPhotoRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'media_id' => 'nullable|integer'
        ];
    }
}
