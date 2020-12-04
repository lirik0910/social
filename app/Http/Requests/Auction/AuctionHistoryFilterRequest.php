<?php


namespace App\Http\Requests\Auction;

use App\GraphQL\Queries\Auction\AuctionsHistory;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Auction;

class AuctionHistoryFilterRequest extends AbstractValidation
{
    protected $dataLocation = 'filter';

    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'type' => 'required|integer|in:' . implode(',', array_keys(AuctionsHistory::availableParams('type'))),
            'status' => 'nullable|integer|in:' . implode(',', array_keys(Auction::availableParams('status'))),
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'status' => null,
        ];
    }
}
