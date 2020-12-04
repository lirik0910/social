<?php

namespace App\Http\Requests\General;

use App\Libraries\GraphQL\AbstractValidation;

class FeedTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'location' => 'array',
            'location.lat' => 'required_with:location|numeric|min:-90|max:90',
            'location.lng' => 'required_with:location|numeric|min:-180|max:180',
            'search_radius' => 'nullable|integer'
        ];
    }
}
