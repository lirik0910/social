<?php

namespace App\GraphQL\Mutations\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Meetings\AcceptMeetingRequest;
use App\Models\Meeting;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AcceptMeeting
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param AcceptMeetingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, AcceptMeetingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $meeting = Meeting::whereId($inputs['id'])->firstOrFail();

        if ($meeting->seller_id !== $context->user()->id) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error'));
        }

        if ($meeting->status !== Meeting::STATUS_NEW) {
            throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error'));
        }

        if (Carbon::now() > $meeting->meeting_date || Carbon::now()->diffInMinutes($meeting->created_at) > 30) {
            throw new GraphQLLogicRestrictException(__('meeting.is_expired'), __('Error'));
        }

        $meeting->status = Meeting::STATUS_ACCEPTED;

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        return $meeting;
    }
}
