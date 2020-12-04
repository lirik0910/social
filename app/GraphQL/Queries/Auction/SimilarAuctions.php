<?php

namespace App\GraphQL\Queries\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Auction;
use App\Traits\PrivacyTrait;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SimilarAuctions
{
    use PrivacyTrait;

    /**
     * Update auction data
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $original_auction = $rootValue['auction'] ?? null;
        $original_auction_profile = $original_auction->user->profile;
        $original_last_bid = $original_auction->last_bid_id ? $original_auction->latest_bid->value : $original_auction->input_bid;

        $query = Auction
            ::active()
            ->where('auctions.id', '!=', $original_auction->id)
            ->leftJoin('profiles', function ($join) {
                $join->on('profiles.user_id', '=', 'auctions.user_id');
            })
            ->leftJoin('auction_bids', function ($join) {
                $join->on('auction_bids.id', '=', 'auctions.last_bid_id');
            })
            ->where('profiles.sex', '=', $original_auction_profile->sex)
            ->where('auctions.city', '=', $original_auction->city)
            ->orderByRaw(
                "abs('auction_bids.value' - {$original_last_bid})"
            )
            ->select(["auctions.*", "profiles.age", "profiles.sex", "auction_bids.value"])
            ->limit(20);

        $this->setIgnoredUsers($query);
        $similar = $query->get();

        if(collect($similar)->count() > 3) {
            $similar = collect($similar)->random(3);
        }

        return $similar;
    }
}
