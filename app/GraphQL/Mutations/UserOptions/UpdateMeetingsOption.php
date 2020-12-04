<?php

namespace App\GraphQL\Mutations\UserOptions;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Meetings\MeetingsOptionRequest;
use App\Models\CharityOrganization;
use App\Models\UserMeetingsOption;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateMeetingsOption
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param MeetingsOptionRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return UserMeetingsOption|mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, MeetingsOptionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $charity_id = Arr::get($inputs, 'charity_organization_id');

        if (!empty($charity_id)) {
            $charity = CharityOrganization
                ::whereId($charity_id)
                ->firstOrFail();

            if (!$charity->available) {
                throw new GraphQLLogicRestrictException(__('charity_organization.not_available'), __('Error'));
            }
        }

        if ($user->meetings_options){
            $meetings_options = $user->meetings_options;
        } else {
            $meetings_options = new UserMeetingsOption();
            $meetings_options->user_id = $user->id;
        }

        $meetings_options->fill($inputs);

        if (!$meetings_options->save()) {
            throw new GraphQLSaveDataException(__('user_meetings_option.update_failed'), __('Error'));
        }

        return $meetings_options;
    }
}
