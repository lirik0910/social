<?php

namespace App\Http\Requests\MeetingReview;

use App\Libraries\GraphQL\AbstractValidation;

class CreateMeetingReviewRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'meeting_id' => 'required|integer',
            'value' => 'required|integer|min:1|max:5',
            'description' => 'nullable|string|max:280',
            'anonymous' => 'nullable|boolean'
        ];
    }
}
