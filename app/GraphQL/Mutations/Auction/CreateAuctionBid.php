<?php

namespace App\GraphQL\Mutations\Auction;

use App\Events\AuctionUpdated;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Auction\CreateAuctionBidRequest;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateAuctionBid
{
    use DynamicValidation, RequestDataValidate;

    /**
     * @param $rootValue
     * @param CreateAuctionBidRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return AuctionBid
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    protected function resolve($rootValue, CreateAuctionBidRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $auction = Auction
            ::whereId($inputs['auction_id'])
            ->firstOrFail();

        if (!$user->can('createBid', $auction)) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error!'));
        }

        $auction_user = $auction->user;

        // Check action`s availability to this user
        $auction_user->isBlocked();

        if ($auction->photo_verified_only == true && !$user->hasFlag(User::FLAG_PHOTO_VERIFIED)) {
            throw new GraphQLLogicRestrictException(__('auction.photo_verification_required'), __('Error!'));
        }

        if ($auction->isEnded()) {
            throw new GraphQLLogicRestrictException(__('auction.already_ended'), __('Error'));
        }

        if (count($auction->bids) == 0) {
            $min_value = $auction->input_bid;
        } else {
            $min_value = ($auction->latest_bid->value ?? $auction->input_bid) + $auction->minimal_step;
        }

        try {
            $value = $this->validatedData($inputs, [
                'value' => 'integer|min:' . ($min_value + 1) . '|max:' . (Auction::PRICE_MAX_VALUE - 1),
            ])['value'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        if ($value > $user->balance) {
            throw new GraphQLLogicRestrictException(__('auction.not_enough_money_in_the_account'), __('Error'));
        }

        $auction_bid = new AuctionBid();
        $auction_bid->user_id = $user->id;
        $auction_bid->auction_id = $auction->id;
        $auction_bid->auction_user_id = $auction_user->id;
        $auction_bid->value = $value;

        if (!$auction_bid->save()) {
            throw new GraphQLSaveDataException(__('auction_bid.create_failed'), __('Error'));
        }

        $auction->last_bid_id = $auction_bid->id;
        $auction->last_bid_user_id = $auction_bid->user_id;
        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.cannot_update_last_bid'), __('Error'));
        }

        if (AuctionBid::where(['user_id' => $user->id, 'auction_id' => $auction->id])->count() < 2) {
            $auction->increment('participants');
        }

        event(new AuctionUpdated($auction, $auction_bid));

        return $auction_bid;
    }
}
