<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\GraphQL\Queries\Admin\CharityOrganization\NativeCharityOrganizations;
use App\Libraries\GraphQL\AbstractValidation;

class NativeCharityOrganizationsRequest extends AbstractValidation
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
            'order_by.dir' => 'in:DESC,ASC',
            'order_by.column' => 'string|in:' . implode(',', array_keys(NativeCharityOrganizations::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.available' => 'boolean',
            'filter.name' => 'string',
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
