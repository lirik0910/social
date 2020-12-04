<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Media;
use App\Models\UserPhotoVerification;
use Illuminate\Foundation\Http\FormRequest;

class UserVerificationPhotosTotalRequest extends AbstractValidation
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
            'date' => 'date|exclude_unless:date_period, array',
            'date_period' => 'array',
            'date_period.from' => 'required_with:date_period|date|lte:date_period.to',
            'date_period.to' => 'required_with:date_period|date|gte:date_period.from',
            'status' => 'integer|in:' . implode(',', array_keys(UserPhotoVerification::availableParams('status'))),
            'decline_reason' => 'integer|in:' . implode(',', array_keys(UserPhotoVerification::availableParams('decline_reason'))),
            'nickname' => 'string'
        ];
    }
}
