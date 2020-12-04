<?php

namespace App\GraphQL\Mutations\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\MeetingReview\CreateMeetingReviewRequest;
use App\Models\Meeting;
use App\Models\MeetingReview;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateMeetingReview
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param CreateMeetingReviewRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return MeetingReview
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateMeetingReviewRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $meeting = Meeting::whereId($inputs['meeting_id'])->firstOrFail();

        if (!$this->user->can('createReview', $meeting)) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error'));
        }

        if (!in_array($meeting->status, [Meeting::STATUS_CONFIRMED, Meeting::STATUS_FAILED])) {
            throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error'));
        }

        if (MeetingReview::where(['user_id' => $this->user->id, 'meeting_id' => $meeting->id])->exists()) {
            throw new GraphQLLogicRestrictException(__('meeting.review_already_exist'), __('Error'));
        }

        $meeting_review = new MeetingReview();
        $meeting_review->user_id = $this->user->id;
        $meeting_review->meeting_id = $meeting->id;

        $meeting_review->fill($inputs);

        if (!$meeting_review->save()) {
            throw new GraphQLSaveDataException(__('meeting.create_review_failed'), __('Error'));
        }

        $this->updateMeetingsRating($meeting, $inputs['value']);

        return $meeting_review;
    }

    /**
     * Recalculate and store new meetings rating for reviewed user
     *
     * @param Meeting $meeting
     * @param int $new_value
     * @return mixed
     */
    public function updateMeetingsRating(Meeting $meeting, int $new_value)
    {
        $reviewed_user_id = $this->user->id === $meeting->user_id ? $meeting->seller_id : $meeting->user_id;
        $reviewed_user = User
            ::whereId($reviewed_user_id)
            ->firstOrFail();

        $meetings_ratings = $reviewed_user->received_meetings_reviews()->get(['meeting_reviews.*']);

        $reviews_values = $meetings_ratings->map(function ($item) {
            return $item->value;
        })->push($new_value);

        $reviewed_user->meetings_rating = round($reviews_values->sum() / count($reviews_values), 2);

        return $reviewed_user->save();
    }
}
