<?php

namespace App\Http\Requests\Admin\Common;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateAdminFilesPresignedUrlsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'type' =>'required|integer|in:' . implode(',', array_keys(MediaHelper::availableParams('file_type'))),
            'files' => 'required|array',
            'files.*' => 'array',
            'files.*.id' => 'nullable|integer',
            'files.*.name' => 'required|string|max:255',
            'files.*.mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
            'files.*.thumbs' => 'nullable|array|min:1'
        ];
    }
}
