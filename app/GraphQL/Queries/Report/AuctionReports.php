<?php

namespace App\GraphQL\Queries\Report;

use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Report;
use App\Traits\DynamicValidation;

class AuctionReports
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return array
     */
    protected function resolve($rootValue, IDRequiredRequest $args)
    {
        $inputs = $args->validated();

        return Report::where('reported_type', 'auctions')
            ->where('reported_id', $inputs['id'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}
