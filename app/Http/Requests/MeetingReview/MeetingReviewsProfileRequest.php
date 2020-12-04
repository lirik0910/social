<?php

namespace App\Http\Requests\MeetingReview;

use App\Libraries\GraphQL\AbstractValidation;

class MeetingReviewsProfileRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'filter' => 'nullable|integer|min:1|max:5',
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'filter' => 0,
            'limit' => 10,
            'offset' => 0
        ];
    }
}
