<?php

namespace App\Http\Requests\MediaPresent;

use App\Libraries\GraphQL\AbstractValidation;

class MediaPresentsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'media_id' => 'required|string',
            'limit' => 'integer',
            'offset' => 'integer',
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
