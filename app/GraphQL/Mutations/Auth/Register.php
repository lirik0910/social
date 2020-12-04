<?php

namespace App\GraphQL\Mutations\Auth;

use App\Helpers\PhoneHelper;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\BlockedUser;
use App\Traits\DynamicValidation;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Register
{
    use DynamicValidation;
    use RequestDataValidate;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param RegistrationRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws AuthenticationException
     */
    protected function resolve($rootValue, RegistrationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        if (!config('app.registration_open')) {
            throw new AuthenticationException(__('Registrations are closed!'));
        }

        $inputs = $args->validated();

        try {
            $this->validatedData(['phone' => $inputs['code'] . $inputs['number']]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        // TODO: remove checking on test environment
        $isTestEnvironment = env('APP_ENV') != "production";

        $verificationCode = $isTestEnvironment ? config('app.phone_verification_default_code') : PhoneHelper::generateVerificationCode();

        $user = new User();
        $user->phone = $inputs['code'] . $inputs['number'];;
        $user->password = Hash::make($inputs['password']);
        $user->phone_verification_code = Hash::make($verificationCode);
        $user->phone_verification_expired_at = Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT);

        // set flags that require user verify phone number and fill required fields
        $user->setFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION | User::FLAG_REQUIRED_FILL_PROFILE);

        if ($user->save()) {
            // once user stored successfully send verification code and return auth data
            if (!$isTestEnvironment) {
                $user->sendSms(__('sms.verification', ['code' => $verificationCode]));
            }

            // Update blocked user id if phone number exists
            BlockedUser::where('phone_number', '=', $user->phone)->update(['blocked_id'=>$user->id]);

            return [
                'token' => $user->createToken('BuyDating Personal Access Client')->accessToken,
                'user' => $user,
            ];
        } else {
            throw new AuthenticationException(__('Unable to register user.'));
        }
    }

    /**
     * Check whether system allowed to send sms on user phone
     *
     * @param string $phoneCode
     * @param string $phoneNumber
     * @throws ValidationException
     */
    protected function validatePhoneData(string $phoneCode, string $phoneNumber)
    {
        if (!PhoneHelper::validatePhone($phoneCode, $phoneNumber)) {
            throw ValidationException::withMessages(['phone' => [__('auth.phone_dont_support')]]);
        }
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'phone' => 'required|unique:users,phone',
        ];
    }
}
