<?php

namespace App\Http\Requests\Media;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateFilePresignedUrlsRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'type' =>'required|integer|in:' . implode(',', array_keys(MediaHelper::availableParams('file_type'))),
            'files' => 'required|array',
            'files.*' => 'array',
            'files.*.name' => 'required|string|max:255',
            'files.*.mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'files.*.description' => 'nullable|string|max:255',
            'files.*.thumbs' => 'nullable|array|min:1'
        ];
    }
}
