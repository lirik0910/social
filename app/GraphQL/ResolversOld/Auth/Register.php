<?php

namespace App\GraphQl\ResolversOld\Auth;


use App\Helpers\PhoneHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use App\Exceptions\GraphQLSaveDataException;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Hash;

class Register
{

    use ValidatesRequests;

    /**
     * Resolve register request. If validation passed method store new user to DB
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Nuwave\Lighthouse\Exceptions\AuthenticationException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function resolve($rootValue, array $args)
    {
        $inputs = $args['data'];
        $inputs['phone'] = $inputs['code'] . $inputs['number'];

        try {
            $this->validate($inputs, $this->rules());
            $this->validatePhoneData($inputs['code'], $inputs['number']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $verificationCode = rand(100000, 999999);

        $user = new User();
        $user->phone = $inputs['phone'];
        $user->password = Hash::make($inputs['password']);
        $user->phone_verification_code = Hash::make($verificationCode);
        $user->phone_verification_expired_at = Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT);

        // set flags that require user verify phone number and fill required fields
        $user->setFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION | User::FLAG_REQUIRED_FILL_PROFILE);

        if ($user->save()) {
            // once user stored successfully send verification code and return auth data
            $user->sendSms(__('sms.verification', ['code' => $verificationCode]));

            return [
                'token' => $user->createToken('BuyDating Personal Access Client')->accessToken,
                'user' => $user,
            ];
        } else {
            throw new AuthenticationException(__('Unable to register user.'));
        }
    }

    /**
     * Resolve sms request. If validation success send SMS and save the confirmation code
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveSms($rootValue, array $args)
    {
        $user = \Auth::user();
        $timeToReset = $user->time_to_reset;

        if ($timeToReset <= 0) {
            $verificationCode = rand(100000, 999999);

            $user->phone_verification_code = Hash::make($verificationCode);
            $user->phone_verification_expired_at = Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT);
            if ($user->save()) {
                $user->sendSms(__('sms.verification', ['code' => $verificationCode]));

                return [
                    'time' => User::RESET_TOKEN_TIMEOUT,
                ];
            } else {
                throw new GraphQLSaveDataException(__('sms.send_validation_code_failed'), __('Error'));
            }
        } else {
            return [
                'time' => $timeToReset,
            ];
        }
    }

    /**
     * If the code is verified update the user status
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveSmsConfirm($rootValue, array $args)
    {
        $user = \Auth::user();
        $inputs = $args['data'];
        $code = $inputs['code'];
        if (Hash::check($code, $user->phone_verification_code)) {
            $user->removeFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION);
            $user->phone_verification_code = null;
            $user->phone_verification_expired_at = null;
            if ($user->save()) {
                return [
                    'user' => $user
                ];
            } else {
                throw new GraphQLSaveDataException(__('sms.save_validation_failed'), __('Error'));
            }
        } else {
            throw new GraphQLValidationException(['code' => [__('sms.validation_failed')]], __('Error'));

        }

    }

    /**
     * Check whether system allowed to send sms on user phone
     *
     * @param string $phoneCode
     * @param string $phoneNumber
     */
    protected function validatePhoneData(string $phoneCode, string $phoneNumber)
    {
        if (!PhoneHelper::validatePhone($phoneCode, $phoneNumber)) {
            throw ValidationException::withMessages(['phone' => [__('auth.phone_dont_support')]]);
        }
    }

    /**
     * Validate request payload
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return mixed
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->getValidationFactory()->make($data, $rules, $messages, $customAttributes)->validate();
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'code' => 'required|in:' . implode(',', PhoneHelper::phoneCodes()),
            'number' => 'required',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
