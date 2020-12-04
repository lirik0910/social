<?php

namespace App\Http\Requests\Profile;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateCustomBackgroundPresignedUrlsRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string|max:255',
            'mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'thumbs' => 'nullable|array|min:1'
        ];
    }
}
