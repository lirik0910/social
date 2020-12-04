<?php

namespace App\Http\Requests\Profile;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class CustomBackgroundUploadStoreRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string|max:255',
            'mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'size' => 'required|string',
            'thumbs' => 'nullable|array|min:1',
        ];
    }
}
