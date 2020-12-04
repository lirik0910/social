<?php


namespace App\GraphQL\Resolvers;


use App\Models\User;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetUserMeetingsReviewsCount
{
    use RequestDataValidate;

    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $args['id'] === Auth::user()->id
            ? Auth::user()
            : User::whereId($args['id'])->firstOrFail();


        $reviews = $user
            ->received_meetings_reviews()
            ->get();

        return $reviews
            ->groupBy('value')
            ->mapWithKeys(function ($item, $key) {
                return [$key => count($item)];
            })
            ->all();
    }
}
