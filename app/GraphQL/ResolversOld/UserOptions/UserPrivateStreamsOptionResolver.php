<?php


namespace App\GraphQL\ResolversOld\UserOptions;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\UserPrivateStreamsOption;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class UserPrivateStreamsOptionResolver
{
    use RequestDataValidate;

    /**
     * Create/Update UserPrivateStreamsOption record
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

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        if($user->private_streams_options){
            $private_streams_options = $user->private_streams_options;
        } else {
            $private_streams_options = new UserPrivateStreamsOption();
            $private_streams_options->user_id = $user->id;
        }

        $private_streams_options->fill($inputs);

        if (!$private_streams_options->save()) {
            throw new GraphQLSaveDataException(__('user_private_streams_option.update_failed'), __('Error'));
        }

        return $private_streams_options;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'tariffing' => 'integer|min:1|max:42949967200',
            'receive_calls' => 'boolean',
            'min_age' => 'integer|min:0|max:99|lte:max_age', // that value must be less than `max_age` or equal
            'max_age' => 'integer|min:0|max:99|gte:min_age', // that value must be greater than `min_age` or equal
            'photo_verified_only' => 'boolean',
            'fully_verified_only' => 'boolean',
        ];

        return $rules;
    }
}
