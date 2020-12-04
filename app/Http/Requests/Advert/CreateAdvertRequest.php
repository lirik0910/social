<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Advert;
use App\Models\Meeting;
use Carbon\Carbon;
use ReflectionException;

class CreateAdvertRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws ReflectionException
     */
    public function rules(): array
    {
        return [
            'type' => 'required|integer|in:' . implode(',', array_keys(Advert::availableParams('type'))),
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::now()->addHour() > $value) {
                        $fail(__('advert.early_meeting'));
                    }

                },
            ],
            #'meeting_date' => 'required|date|after:' . Carbon::now(),
            'price' => 'required|integer|min:0|max:' . Advert::PRICE_MAX_VALUE,
            'min_age' => 'required|integer|min:0|max:99|lte:max_age',
            'max_age' => 'required|integer|min:0|max:99|gte:min_age',
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            //     'charity_organization_id' => 'nullable|integer|exists:charity_organizations,id',
            'photo_verified_only' => 'required|boolean',
            'preview' => 'nullable|string|max:250',
            'address' => 'required|string|max:250',
            'safe_deal_only' => 'required|boolean'
        ];
    }
}
