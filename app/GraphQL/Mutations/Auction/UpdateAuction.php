<?php

namespace App\GraphQL\Mutations\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Auction\UpdateAuctionRequest;
use App\Models\Auction;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateAuction
{
    use RequestDataValidate;

    /**
     * @param $rootValue
     * @param UpdateAuctionRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateAuctionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $auction = Auction::whereId($inputs['id'])->firstOrFail();

        if ($auction->bids->isNotEmpty()) {
            throw new GraphQLLogicRestrictException(__('auction.cannot_update'), __('Error'));
        }

        $auction->fill($inputs);

        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        return $auction;
    }
}
