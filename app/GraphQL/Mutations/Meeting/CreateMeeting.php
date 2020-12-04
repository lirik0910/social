<?php

namespace App\GraphQL\Mutations\Meeting;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Meetings\CreateMeetingRequest;
use App\Models\Meeting;
use App\Models\User;
use App\Models\UserMeetingsOption;
use App\Traits\DynamicValidation;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateMeeting
{
    use DynamicValidation, RequestDataValidate;

    /**
     * @var UserMeetingsOption
     */
    protected $seller_meetings_options;

    /**
     * @param $rootValue
     * @param CreateMeetingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Meeting
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    protected function resolve($rootValue, CreateMeetingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        if ($user->id == $inputs['seller_id']) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error!'));
        }

        $seller = User
            ::whereId($inputs['seller_id'])
            ->with('meetings_options')
            ->firstOrFail();

        // Check action`s availability to this user
        $seller->isBlocked();

        $this->seller_meetings_options = $seller->meetings_options;

        $additional_validation_rules = $this->getAdditionalValidationRules();

        try {
            $this->validatedData($inputs, $additional_validation_rules);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        if (!empty($this->seller_meetings_options->photo_verified_only) && !$user->hasFlag(User::FLAG_PHOTO_VERIFIED)) {
            throw new GraphQLLogicRestrictException(__('meeting.photo_verified_only'), __('Error'));
        }

        $meeting = new Meeting();
        $meeting->user_id = $user->id;
        $meeting->seller_id = $inputs['seller_id'];
        $meeting->status = Meeting::STATUS_NEW;
        $meeting->charity_organization_id = $this->seller_meetings_options->charity_organization_id;

        $meeting->fill($inputs);

        if ($meeting->safe_deal && $user->balance < $meeting->price) {
            throw new GraphQLSaveDataException(__('advert.insufficient_funds_in_the_account'), __('Error'));
        }

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.create_failed'), __('Error'));
        }

        return $meeting;
    }

    protected function getAdditionalValidationRules()
    {
        $rules = [
            'price' => 'integer|min:' . $this->seller_meetings_options->minimal_price,
        ];

        if($this->seller_meetings_options->safe_deal_only) {
            $rules['safe_deal'] = 'boolean|accepted';
        }

        return $rules;
    }
}
