<?php

namespace App\Http\Requests\Meetings;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;
use Carbon\Carbon;

class CreateMeetingRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'seller_id' => 'required|string',
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        if (Carbon::now()->addMinutes(30) < $value) {
                            $fail(__('meeting.early_date'));
                        }
                    },
                ],
            #'meeting_date' => 'required|date|after:' . Carbon::now()->addMinutes(30),
            'price' => 'required|integer|max:' . Meeting::PRICE_MAX_VALUE,
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            'safe_deal' => 'required|boolean',
            'address' => 'required|string|max:255'
        ];
    }
}
