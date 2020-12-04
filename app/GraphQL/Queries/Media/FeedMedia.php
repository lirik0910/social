<?php

namespace App\GraphQL\Queries\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\General\FeedTotalRequest;
use App\Http\Requests\Media\MediaFeedRequest;
use App\Libraries\GraphQL\AbstractFeedSelections;
use App\Models\Media;
use App\Traits\DynamicValidation;
use App\Traits\PrivacyTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Auth;

class FeedMedia extends AbstractFeedSelections
{
    use DynamicValidation, PrivacyTrait;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  MediaFeedRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, MediaFeedRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->setMainProperties($inputs);

        $this->content_type = self::CONTENT_TYPE_MEDIA;

        $results = $this->getResultsCustom() ?? collect([]);

        return [
            'results' => $results
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
        $instance = Media
            ::where(function ($query) {
                $query->notBanned();
            });

        if(!empty($this->boundary_coordinates)) {
            $instance
                ->whereBetween('profiles.lat', [$this->boundary_coordinates['min_lat'], $this->boundary_coordinates['max_lat']])
                ->whereBetween('profiles.lng', [$this->boundary_coordinates['min_lng'], $this->boundary_coordinates['max_lng']]);
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

        $this->content_type = self::CONTENT_TYPE_MEDIA;

        return $this->getResultsTotalCount();
    }
}
