<?php

namespace App\Http\Requests\PhotoVerification;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateVerificationPhotoPresignedUrlsRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'id' => 'integer',
            'name' => 'required|string|max:255',
            'mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes())
        ];
    }
}
