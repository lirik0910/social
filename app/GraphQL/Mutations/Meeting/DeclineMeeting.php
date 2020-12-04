<?php

namespace App\GraphQL\Mutations\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Meetings\DeclineMeetingRequest;
use App\Models\Meeting;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeclineMeeting
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param DeclineMeetingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Meeting
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, DeclineMeetingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $meeting = Meeting::whereId($inputs['id'])->firstOrFail();

        if (!in_array($context->user()->id, [$meeting->seller_id, $meeting->user_id])) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error'));
        }

        if ($meeting->status === Meeting::STATUS_ACCEPTED) {
            $meeting->status = Meeting::STATUS_DECLINED;

            if (!$meeting->save()) {
                throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
            }
        } else {
            if (!$meeting->update(['status' => Meeting::STATUS_DECLINED, 'deleted_at' => DB::raw('NOW()')])) {
                throw new GraphQLSaveDataException(__('meeting.delete_failed'), __('Error'));
            }

            // as meeting removed we should send empty data to graphql
            $meeting = new Meeting();
        }

        return $meeting;
    }
}
