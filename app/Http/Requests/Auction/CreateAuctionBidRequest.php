<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;

class CreateAuctionBidRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'auction_id' => 'required|integer',
            'value' => 'required|integer'
        ];
    }
}
