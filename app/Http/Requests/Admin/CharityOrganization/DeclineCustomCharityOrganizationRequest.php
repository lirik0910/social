<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\CharityOrganization;

class DeclineCustomCharityOrganizationRequest extends AbstractValidation
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
            'id' => 'required|string',
            'moderation_declined_reason' => 'integer|in:' . implode(',', array_keys(CharityOrganization::availableParams('moderation_declined_reason'))),
        ];
    }
}
