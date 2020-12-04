<?php

namespace App\Http\Requests\Admin\ProfilesBackground;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class GenerateProfilesBackgroundsPresignedUrlsRequest extends AbstractValidation
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
            'files.*' => 'array',
            'files.*.id' => 'nullable|integer',
            'files.*.name' => 'required|string|max:255',
            'files.*.mimetype' => 'required|string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
        ];
    }
}
