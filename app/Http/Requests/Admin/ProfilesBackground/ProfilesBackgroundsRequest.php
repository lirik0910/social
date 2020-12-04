<?php

namespace App\Http\Requests\Admin\ProfilesBackground;

use App\GraphQL\Queries\Admin\ProfilesBackground\ProfilesBackgrounds;
use App\Libraries\GraphQL\AbstractValidation;

class ProfilesBackgroundsRequest extends AbstractValidation
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
            'order_by' => 'array',
            'order_by_dir' => 'in:DESC,ASC',
            'filter' => 'array',
            'filter.user' => 'string|max:18',
            'filter.custom' => 'boolean',
            'filter.available' => 'boolean',
        ];
    }

    /**
     * Return default values for request params
     *
     * @return array|int[]
     */
    protected function defaultValues() : array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'order_by_dir' => 'DESC',
        ];
    }
}
