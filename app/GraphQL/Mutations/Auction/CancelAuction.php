<?php

namespace App\GraphQL\Mutations\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Auction\CancelAuctionRequest;
use App\Models\Auction;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use Event;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CancelAuction
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CancelAuctionRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CancelAuctionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();
        $auction = Auction::whereId($inputs['id'])->firstOrFail();

        if (!$user->can('cancel', $auction)) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error!'));
        }

        if ($auction->isEnded()) {
            throw new GraphQLLogicRestrictException(__('auction.already_ended'), __('Error'));
        }

        $auction->cancelled_at = Carbon::now();

        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        return $auction;
    }
}
