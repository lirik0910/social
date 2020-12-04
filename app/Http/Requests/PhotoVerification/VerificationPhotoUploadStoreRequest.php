<?php

namespace App\Http\Requests\PhotoVerification;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class VerificationPhotoUploadStoreRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'media_id' => 'nullable|integer|exists:media,id',
            'name' => 'required|string|max:255',
            'mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'size' => 'string'
        ];
    }
}
