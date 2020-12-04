<?php

namespace App\Http\Requests\MeetingReview;

use App\Libraries\GraphQL\AbstractValidation;

class MeetingReviewsProfileTotalRequest extends AbstractValidation
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
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'filter' => 0,
        ];
    }
}
