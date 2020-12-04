<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;
use Carbon\Carbon;

class AuctionsProfileRequest extends AbstractValidation
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
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 5,
            'offset' => 0
        ];
    }
}
