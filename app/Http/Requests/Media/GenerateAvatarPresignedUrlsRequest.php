<?php

namespace App\Http\Requests\Media;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateAvatarPresignedUrlsRequest extends AbstractValidation
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
            'thumbs' => 'nullable|array|min:1'
        ];
    }
}
