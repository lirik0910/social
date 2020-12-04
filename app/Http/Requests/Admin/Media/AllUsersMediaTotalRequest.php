<?php

namespace App\Http\Requests\Admin\Media;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Media;

class AllUsersMediaTotalRequest extends AbstractValidation
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
            'nickname' => 'string|max:18',
            'type' => 'integer|in:' . implode(',', array_keys(Media::availableParams('type'))),
            'active' => 'boolean',
            'mimetype' => 'string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
        ];
    }
}
