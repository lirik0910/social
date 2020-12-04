<?php

namespace App\Http\Requests\Media;

use App\Libraries\GraphQL\AbstractValidation;
use Carbon\Carbon;

class MediaFeedRequest extends AbstractValidation
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
            'limit' => 50,
            'offset' => 0,
        ];
    }
}
