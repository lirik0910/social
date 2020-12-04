<?php


namespace App\GraphQL\Resolvers;


use App\Models\UserMeetingsOption;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetMaxMeetingPrice
{
    /**
     * Default meeting max price (if there are no more users on service)
     */
    const DEFAULT_MAX_PRICE = 5000;

    /**
     * Get meeting max price for searching page
     *
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @return int
     */
    public function resolve()
    {
        $user = Auth::user();

        $max_price_meeting_options = UserMeetingsOption
            ::where('user_id', '!=', $user->id)
            ->orderByDesc('minimal_price')
            ->first();

        return $max_price_meeting_options ? $max_price_meeting_options->minimal_price : self::DEFAULT_MAX_PRICE;
    }
}
