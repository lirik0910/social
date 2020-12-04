<?php

namespace App\GraphQL\Queries\Meeting;

use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Meeting;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;

class AuctionMeeting
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return mixed
     */
    protected function resolve($rootValue, IDRequiredRequest $args)
    {
        $inputs = $args->validated();

        $auction_id = Arr::get($inputs, 'id');

        return Meeting
            ::where('inherited_type', Meeting::INHERITED_TYPE_AUCTIONS)
            ->where('inherited_id', $auction_id)
            ->first();
    }
}
