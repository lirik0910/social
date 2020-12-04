<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\UserBan;
use Carbon\Carbon;

class BanUserRequest extends AbstractValidation
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
            'reason' => 'required_without:other_reason|string|in:' . implode(',', array_keys(UserBan::availableParams('reason'))),
            'other_reason' => 'exclude_if:reason,true|string',
            'unbanned_date' => 'date|after:' . Carbon::now()->addHour()
        ];
    }
}
