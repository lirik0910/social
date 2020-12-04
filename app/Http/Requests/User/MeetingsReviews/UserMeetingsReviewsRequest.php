<?php

namespace App\Http\Requests\User\MeetingsReviews;

use App\Libraries\GraphQL\AbstractValidation;

class UserMeetingsReviewsRequest extends AbstractValidation
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
            'count' => 'required|array',
            'count.limit' => 'required_with:count|integer',
            'count.offset' => 'required_with:count|integer',
            'filter' => 'integer|min:1|max:5'
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
        ];
    }
}
