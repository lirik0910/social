<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\CharityOrganization;

class CustomCharityOrganizationsRequest extends AbstractValidation
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
            'filter.nickname' => 'string|max:12',
            'filter.name' => 'string',
            'filter.moderation_status' => 'integer|in:' . implode(',', array_keys(CharityOrganization::availableParams('moderation_status'))),
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
        ];
    }
}
