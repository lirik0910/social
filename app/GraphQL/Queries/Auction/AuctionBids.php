<?php

namespace App\GraphQL\Queries\Auction;

use App\Http\Requests\Auction\AuctionBidsRequest;
use App\Http\Requests\Auction\AuctionBidsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\AuctionBid;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AuctionBids extends AbstractSelection
{
    use DynamicValidation;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @param $rootValue
     * @param AuctionBidsRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     */
    protected function resolve($rootValue, AuctionBidsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->id = Arr::get($inputs, 'id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return AuctionBid
            ::where('auction_id', $this->id);
    }

    /**
     * Get total count
     *
     * @param $rootValue
     * @param AuctionBidsTotalRequest $args
     * @return mixed
     */
    protected function getTotal($rootValue, AuctionBidsTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->id = Arr::get($inputs, 'id');

        return $this->getResultsTotalCount();
    }
}
