<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\CharityOrganization;

class CustomCharityOrganizationsTotalRequest extends AbstractValidation
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
            'nickname' => 'string|max:12',
            'name' => 'string',
            'moderation_status' => 'integer|in:' . implode(',', array_keys(CharityOrganization::availableParams('moderation_status'))),
        ];
    }
}
