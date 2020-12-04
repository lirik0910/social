<?php

namespace App\GraphQL\Queries\Profile;

use App\Models\ProfilesBackground;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ProfileBackgrounds
{
    /**
     * Return list of available profile backgrounds
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        return ProfilesBackground
            ::where(function ($query) use ($user) {
                $query
                    ->whereNull('user_id')
                    ->where('available', true);
            })
            ->orWhere('user_id', $user->id)
            ->orderByDesc('user_id')
            ->get();
    }
}
