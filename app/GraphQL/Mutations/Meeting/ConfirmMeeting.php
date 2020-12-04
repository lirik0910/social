<?php

namespace App\GraphQL\Mutations\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Meetings\ConfirmMeetingRequest;
use App\Models\Meeting;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use GraphQL\Type\Definition\ResolveInfo;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class ConfirmMeeting
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ConfirmMeetingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    protected function resolve($rootValue, ConfirmMeetingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $meeting = Meeting::whereId($inputs['id'])->firstOrFail();

        if ($meeting->user_id !== $context->user()->id) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error'));
        }

        if ($meeting->status !== Meeting::STATUS_ACCEPTED) {
            throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error'));
        }

        $confirmation_code = Arr::get($inputs, 'confirmation_code');

        if ((env('APP_ENV') != "production" && $confirmation_code === '111111') || $confirmation_code === decrypt($meeting->confirmation_code)) {
            $meeting->status = Meeting::STATUS_CONFIRMED;

            if (!$meeting->save()) {
                throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
            }

            return $meeting;

        } else {
            throw new GraphQLLogicRestrictException(__('meeting.code_validation_failed'), __('Error'));
        }
    }
}
