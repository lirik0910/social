<?php

namespace App\GraphQL\Queries\MeetingReview;

use App\Http\Requests\MeetingReview\MeetingReviewsProfileRequest;
use App\Http\Requests\MeetingReview\MeetingReviewsProfileTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\MeetingReview;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ProfileMeetingReviews extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Page owner`s ID
     *
     * @var integer|string
     */
    protected $page_owner_id;

    /**
     * Value of the selecting meeting as filter param
     *
     * @var integer
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  MeetingReviewsProfileRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, MeetingReviewsProfileRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->page_owner_id = Arr::get($inputs, 'id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    public function getBaseQuery()
    {
        $instance = MeetingReview
            ::where('meeting_reviews.user_id', '!=', $this->page_owner_id)
            ->whereHas('meeting', function ($query) {
                $query
                    ->where('meetings.user_id', '=', $this->page_owner_id)
                    ->orWhere('meetings.seller_id', '=', $this->page_owner_id);
            })
            ->leftJoin('profiles', 'profiles.user_id', '=', 'meeting_reviews.user_id')
            ->select(['meeting_reviews.*'])
            ->selectRaw('TIMESTAMPDIFF(YEAR, profiles.age, CURDATE()) as years');

        if(!empty($this->filter)) {
            $instance->where('meeting_reviews.value', '=', $this->filter);
        }

        return $instance;
    }

    /**
     * Get results total count for base query
     *
     * @param $rootValue
     * @param MeetingReviewsProfileTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, MeetingReviewsProfileTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->page_owner_id = Arr::get($inputs, 'id');
        $this->filter = Arr::get($inputs, 'filter');

        return $this->getResultsTotalCount();
    }
}
