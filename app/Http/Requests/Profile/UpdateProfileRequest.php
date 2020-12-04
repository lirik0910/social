<?php

namespace App\Http\Requests\Profile;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Profile;
use Carbon\Carbon;

class UpdateProfileRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules(): array
    {
        $user_id = \Auth::user()->id;
        $before = Carbon::now()->subYears(Profile::MIN_AVAILABLE_AGE)->toDateString();
        $after = Carbon::now()->subYears(Profile::MAX_AVAILABLE_AGE)->toDateString();
        return [
            'nickname' => "sometimes|required|string|min:3|max:12|unique:users,nickname," . $user_id,
            'sex' => 'sometimes|required|integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
            'age' => 'sometimes|required|date|before_or_equal:' . $before . '|after_or_equal:' . $after,
            'dating_preference' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('preference'))),
            'country' => 'nullable|string',
            'region' => 'nullable|string',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric|min:-90|max:90',
            'lng' => 'nullable|numeric|min:-180|max:180',
            'name' => 'nullable|string|min:3',
            'surname' => 'nullable|string|min:3',
            'height' => 'nullable|integer|min:40',
            'physique' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('physique'))),
            'appearance' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('appearance'))),
            'eye_color' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('eye_color'))),
            'hair_color' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('hair_color'))),
            'occupation' => 'nullable|string',
            'marital_status' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('marital_status'))),
            'kids' => 'nullable|boolean',
            'languages' => 'nullable|array|min:1',
            'smoking' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('smoking'))),
            'alcohol' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('alcohol'))),
            'about' => 'nullable|string',
            'email' => "nullable|email|unique:users,email," . $user_id,
            'chat_price' => "nullable|numeric|min:0",
        ];
    }
}
