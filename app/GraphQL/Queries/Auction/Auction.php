<?php

namespace App\GraphQL\Queries\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Auction as Model;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Auction
{
    use DynamicValidation;

    /**
     * Get one auction
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param IDRequiredRequest $args The arguments that were passed into the field.
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        if ($user->role !== User::ROLE_USER && $resolveInfo->fieldName === 'auctionInfo') {
            AdminPermissionsHelper::check('auction_info', $user);
        }

        $id = Arr::get($args->validated(), 'id');

        $auction = Model
            ::where('auctions.id', $id)
            ->leftJoin('profiles', 'profiles.user_id', '=', 'auctions.user_id')
            ->leftJoin('auction_bids', 'auction_bids.id', '=', 'auctions.last_bid_id')
            ->select(["auctions.*", "profiles.age", "profiles.sex", "auction_bids.value"])
            ->firstOrFail();

        return [
            'auction' => $auction
        ];
    }
}
