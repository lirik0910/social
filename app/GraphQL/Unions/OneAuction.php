<?php

namespace App\GraphQL\Unions;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class OneAuction
{
    /**
     * The type registry.
     *
     * @var \Nuwave\Lighthouse\Schema\TypeRegistry
     */
    protected $typeRegistry;

    /**
     * Constructor.
     *
     * @param  \Nuwave\Lighthouse\Schema\TypeRegistry  $typeRegistry
     * @return void
     */
    public function __construct(TypeRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * Decide which GraphQL type a resolved value has.
     *
     * @param  mixed  $rootValue The value that was resolved by the field. Usually an Eloquent model.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return \GraphQL\Type\Definition\Type
     */
    public function __invoke($rootValue, GraphQLContext $context, ResolveInfo $resolveInfo): Type
    {
        $user = Auth::user();

        if($user->id !== $rootValue->user_id && $user->role === User::ROLE_USER) {
            if($rootValue->location_for_winner_only == true) {
                if(!$rootValue->isEnded() || $rootValue->last_bid_user_id !== $user->id) {
                    $name = 'GuestAuctionPage';
                }
            }
        }

        $name = $name ?? 'Auction';

        return $this->typeRegistry->get($name);
    }
}
