<?php

namespace App\GraphQL\Mutations\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Auction\CreateAuctionRequest;
use App\Models\Auction;
use App\Models\CharityOrganization;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateAuction
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateAuctionRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Auction
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateAuctionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $charity_id = Arr::pull($inputs, 'charity_organization_id');

        if (!empty($charity_id)) {
            $charity = CharityOrganization
                ::whereId($charity_id)
                ->firstOrFail();

            if (!$charity->available) {
                throw new GraphQLLogicRestrictException(__('charity_organization.not_available'), __('Error'));
            }
        }

        if ($user->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
            throw new GraphQLLogicRestrictException(__('auction.cannot_create_for_private'), __('Error!'));
        }

        if (!$user->image) {
            throw new GraphQLLogicRestrictException(__('auction.avatar_not_exist'), __('Error'));
        }

        $auction = new Auction();
        $auction->user_id = $user->id;

        $auction->fill($inputs);

        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        return $auction;
    }
}
