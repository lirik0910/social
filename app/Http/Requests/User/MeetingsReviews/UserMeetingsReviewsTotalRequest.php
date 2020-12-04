<?php

namespace App\Http\Requests\User\MeetingsReviews;

use App\Libraries\GraphQL\AbstractValidation;

class UserMeetingsReviewsTotalRequest extends AbstractValidation
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
            'filter' => 'integer|min:1|max:5'
        ];
    }
}
