<?php

namespace App\GraphQL\ResolversOld\Profile;

use App\Helpers\LanguageHelper;
use App\Models\Profile;
use App\Models\User;
use App\Exceptions\GraphQLSaveDataException;
use Carbon\Carbon;
use App\Traits\RequestDataValidate;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class UpdateProfileResolver
{
    use RequestDataValidate;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var Profile
     */
    protected $profile;

    /**
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolve($rootValue, array $args)
    {
        $this->user = \Auth::user();
        $this->profile = $this->user->profile;

        // fetching valid data if validation success
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        // if user change nickname store it to User model
        if (array_key_exists('nickname', $inputs)) {
            $this->user->nickname = $inputs['nickname'];
        }

        // if user change email store it to User model
        if (array_key_exists('email', $inputs)) {
            $this->user->email = $inputs['email'];
        }

        // filter received ids
        if (array_key_exists('languages', $inputs)) {
            $inputs['languages'] = LanguageHelper::filterIds($inputs['languages']) ? : NULL;
        }

        // store data
        $this->profile->fill($inputs);

        // trying to save changes
        if (!$this->user->save() || !$this->profile->save()) {
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        }

        return [
            'user'    => $this->user,
            'profile' => $this->profile,
        ];

    }

    /**
     * Create profile referenced to auth user and store main data
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function mainInfoResolve($rootValue, array $args)
    {
        $user = \Auth::user();
        $profile = new Profile();
        $profile->user_id = $user->id;

        // fetching valid data if validation success
        try {
            $inputs = $this->validatedData(
                $args['data'],
                $this->mainInfoRules()
            );
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        // if user change nickname store it to User model
        if (array_key_exists('nickname', $inputs)) {
            $user->nickname = $inputs['nickname'];
        }

        // store data
        $profile->fill($inputs);
        $user->removeFlag(User::FLAG_REQUIRED_FILL_PROFILE);

        // trying to save changes
        if (!$profile->save()) {
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        } else if (!$user->save()) { // failed to change flag
            $profile->delete();
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        }

        return [
            'user'    => $user,
            'profile' => $profile,
        ];
    }

    /**
     * List of general validation rules
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'nickname'          => "sometimes|required|string|min:3|max:12|unique:users,nickname,{$this->user->id}",
            'sex'               => 'sometimes|required|integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
            'dating_preference' => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('preference'))),
            'country'           => 'nullable|string',
            'region'            => 'nullable|string',
            'address'           => 'nullable|string',
            'lat'               => 'nullable|numeric|min:-90|max:90',
            'lng'               => 'nullable|numeric|min:-180|max:180',
            'name'              => 'nullable|string|min:3',
            'surname'           => 'nullable|string|min:3',
            'height'            => 'nullable|integer|min:40', // todo: need confirmation
            'physique'          => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('physique'))),
            'appearance'        => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('appearance'))),
            'eye_color'         => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('eye_color'))),
            'hair_color'        => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('hair_color'))),
            'occupation'        => 'nullable|string',
            'marital_status'    => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('marital_status'))),
            'kids'              => 'nullable|boolean',
            'languages'         => 'nullable|array|min:1',
            'smoking'           => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('smoking'))),
            'alcohol'           => 'nullable|integer|in:' . implode(',', array_keys(Profile::availableParams('alcohol'))),
            'about'             => 'nullable|string',
            'email'             => "nullable|email|unique:users,email,{$this->user->id}",
        ];
    }

    /**
     * List of main info validation rules
     *
     * @return array
     */
    protected function mainInfoRules()
    {
        return [
            'nickname'          => 'required|string|min:3|max:12|unique:users,nickname',
            'age'               => 'required|date|before_or_equal:' . (Carbon::now()->subYear()->toDateString()) . '|after_or_equal:' . (Carbon::now()->subYears(100)->toDateString()),
            'sex'               => 'required|integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
           // 'dating_preference' => 'required|integer|in:' . implode(',', array_keys(Profile::availableParams('preference'))),
        ];
    }
}
