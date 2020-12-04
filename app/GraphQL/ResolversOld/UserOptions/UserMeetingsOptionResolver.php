<?php


namespace App\GraphQL\ResolversOld\UserOptions;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\UserMeetingsOption;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class UserMeetingsOptionResolver
{
    use RequestDataValidate;

    /**
     * Create/Update UserMeetingOption record
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveUpdate($rootValue, array $args)
    {
        $user = Auth::user();

        if ($user->meetings_options){
            $meetings_options = $user->meetings_options;
        } else {
            $meetings_options = new UserMeetingsOption();
            $meetings_options->user_id = $user->id;
        }

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meetings_options->fill($inputs);

        if (!$meetings_options->save()) {
            throw new GraphQLSaveDataException(__('user_meetings_option.update_failed'), __('Error'));
        }

        return $meetings_options;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'minimal_price' => 'integer|min:0|max:42949967200', // TODO: confirm max value
            'min_age' => 'integer|min:0|max:99', #TODO add custom validation for less or equal than max_age field
            'max_age' => 'integer|min:0|max:99', #TODO add custom validation for greater or equal than min_age field
            'safe_deal_only' => 'boolean',
            'photo_verified_only' => 'boolean',
            'fully_verified_only' => 'boolean',
            'charity_organization_id' => 'nullable|integer|exists:charity_organizations,id',
        ];

        return $rules;
    }
}
