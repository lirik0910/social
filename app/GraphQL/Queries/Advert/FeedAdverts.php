<?php

namespace App\GraphQL\Queries\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\Advert\AdvertsFeedRequest;
use App\Http\Requests\General\FeedTotalRequest;
use App\Libraries\GraphQL\AbstractFeedSelections;
use App\Models\Advert;
use App\Traits\DynamicValidation;
use App\Traits\PrivacyTrait;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class FeedAdverts extends AbstractFeedSelections
{
    use DynamicValidation, PrivacyTrait;

    /**
     * Border date (end_at field must be greater than this date) for auctions searching
     *
     * @var string
     */
    protected $border_date;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  AdvertsFeedRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, AdvertsFeedRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->setMainProperties($inputs);

        $this->content_type = self::CONTENT_TYPE_ADVERTS;
        $this->border_date = $inputs['border_date'];

        $results = $this->getResultsCustom() ?? collect([]);

        return [
            'results' => $results,
            'border_date' => $this->border_date
        ];
    }

    /**
     * Get selection base query instance
     *
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    public function getBaseQuery()
    {
        $instance = Advert
            ::active($this->border_date)
            ->where('min_age', '<=', $this->profile->age->diffInYears(Carbon::now()))
            ->where('max_age', '>=', $this->profile->age->diffInYears(Carbon::now()));

        if(!empty($this->boundary_coordinates)) {
            $instance
                ->whereBetween('adverts.location_lat', [$this->boundary_coordinates['min_lat'], $this->boundary_coordinates['max_lat']])
                ->whereBetween('adverts.location_lng', [$this->boundary_coordinates['min_lng'], $this->boundary_coordinates['max_lng']]);
        }

        $instance
            ->leftJoin('profiles', $this->content_type . '.user_id', '=', 'profiles.user_id')
            ->inRandomOrder()
            ->select([$this->content_type . '.*']);

        return $this->setIgnoredUsers($instance);
    }

    /**
     * Get total count for this selection
     *
     * @param $rootValue
     * @param FeedTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, FeedTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->setMainProperties($inputs);

        $this->border_date = $rootValue['border_date'];
        $this->content_type = self::CONTENT_TYPE_ADVERTS;

        return $this->getResultsTotalCount();
    }
}
