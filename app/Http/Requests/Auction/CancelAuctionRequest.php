<?php

namespace App\Http\Requests\Auction;

use App\Libraries\GraphQL\AbstractValidation;

class CancelAuctionRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer'
        ];
    }
}
