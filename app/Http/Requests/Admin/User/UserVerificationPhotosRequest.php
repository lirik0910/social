<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Media;
use App\Models\UserPhotoVerification;

class UserVerificationPhotosRequest extends AbstractValidation
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
            'filter.date' => 'date',
            'filter.date_period' => 'array',
            'filter.date_period.from' => 'required_with:filter.date_period|date|lte:filter.date_period.to',
            'filter.date_period.to' => 'required_with:filter.date_period|date|gte:filter.date_period.from',
            'filter.status' => 'integer|in:' . implode(',', array_keys(UserPhotoVerification::availableParams('status'))),
            'filter.decline_reason' => 'integer|in:' . implode(',', array_keys(UserPhotoVerification::availableParams('decline_reason'))),
            'filter.nickname' => 'string'
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
