<?php

namespace App\Http\Requests\Profile;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Profile;
use Carbon\Carbon;

class CreateProfileRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        $before = (Carbon::now()->subYears(Profile::MIN_AVAILABLE_AGE)->toDateString());
        $after = (Carbon::now()->subYears(Profile::MAX_AVAILABLE_AGE)->toDateString());
        return [
            'nickname' => 'required|string|min:3|max:12|unique:users,nickname',
            'age' => 'required|date|before_or_equal:' . $before . '|after_or_equal:' . $after,
            'sex' => 'required|integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
        ];
    }
}
