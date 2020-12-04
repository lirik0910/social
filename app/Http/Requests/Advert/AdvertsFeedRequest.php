<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;
use Carbon\Carbon;


class AdvertsFeedRequest extends AbstractValidation
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
            'border_date' => 'date',
            'viewed_ids' => 'array',
            'location' => 'array',
            'location.lat' => 'required_with:location|numeric|min:-90|max:90',
            'location.lng' => 'required_with:location|numeric|min:-180|max:180',
            'search_radius' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'border_date' => Carbon::now(),
        ];
    }
}
