<?php

namespace App\Http\Requests\Admin\Media;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\UserPhotoVerification;

class RejectVerifyingRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
            'decline_reason' => 'integer|in:' . implode(',', array_keys(UserPhotoVerification::availableParams('decline_reason'))),
        ];
    }
}
