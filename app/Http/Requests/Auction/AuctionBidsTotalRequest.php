<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;

class AuctionBidsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:auctions'
        ];
    }
}
