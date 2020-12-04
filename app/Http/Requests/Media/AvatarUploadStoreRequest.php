<?php

namespace App\Http\Requests\Media;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class AvatarUploadStoreRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string|max:255',
            'mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'size' => 'required|string'
        ];
    }
}
