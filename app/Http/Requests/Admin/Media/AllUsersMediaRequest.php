<?php

namespace App\Http\Requests\Admin\Media;

use App\GraphQL\Queries\Admin\Media\AllUsersMedia;
use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Media;

class AllUsersMediaRequest extends AbstractValidation
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
            'limit' => 'integer',
            'offset' => 'integer',
            'order_by_dir' => 'in:DESC,ASC',
            'filter' => 'array',
            'filter.nickname' => 'string|max:18',
            'filter.type' => 'integer|in:' . implode(',', array_keys(Media::availableParams('type'))),
            'filter.active' => 'boolean',
            'filter.mimetype' => 'string|in:' . implode(',', MediaHelper::getAvailableMimetypes()),
        ];
    }

    protected function defaultValues() : array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'order_by_dir' => 'DESC',
        ];
    }
}
