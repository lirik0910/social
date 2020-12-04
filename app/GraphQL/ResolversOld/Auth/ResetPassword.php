<?php


namespace App\GraphQL\ResolversOld\Auth;


use App\Models\User;
use \App\Models\PasswordReset;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class ResetPassword
{
    use ValidatesRequests;

    /**
     * Allow resolve request for generating reset token
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     *
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function run($rootValue, array $args)
    {
        $inputs = $args['data'];

        try {
            $this->validate($inputs, ['phone' => $this->rules()['phone']]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), "Input validation failed");
        }

        $user = User::getUserByPhone($inputs['phone']);

        // check whether reset token already exists and can be regenerate
        $time = $this->hasActiveResetRequest($user->phone);

        // if token does not exists or can be generated
        if (!$time) {
            $time = $this->saveResetRequest($user);
        }

        return [
            'time' => $time,
        ];
    }


    /**
     * Allow resolve reset password request
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     *
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function confirm($rootValue, array $args)
    {

        $inputs = $args['data'];

        try {
            $this->validate($inputs, $this->rules());
            $user = User::getUserByPhone($inputs['phone']);

            // trying to find reset data by phone number
            $lastResetData = $this->fetchResetDate($inputs['phone']);

            // check whether token much with stored hash
            $this->checkToken($inputs['token'], $lastResetData->token);

            // trying update user password
            $this->resetPassword($user, $inputs['password']);

            // remove all previous attempts for current phone number
            PasswordReset::where('phone', $inputs['phone'])->delete();

            return [
                'status' => 'success'
            ];

        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), "Input validation failed");
        }
    }

    /**
     * Validation rules
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'phone'    => 'required|exists:users,phone',
            'token'    => 'required|integer',
            'password' => 'required|confirmed|min:8'
        ];
    }

    /**
     * Validate request data
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
     * Return time in seconds when reset token can be regenerate
     * if reset data already exist for phone number and refresh
     * time has not be expired or false if token can be generate/regenerate
     *
     * @param string $phone
     *
     * @return integer
     */
    protected function hasActiveResetRequest(string $phone)
    {
        try {
            $resetData = $this->fetchResetDate($phone);
            $timeToExpired = $resetData->expired_at->diffInSeconds();

            // checking whether reset token can be regenerate
            $timeToReset = $timeToExpired - User::EXPIRED_TOKEN_TIMEOUT + User::RESET_TOKEN_TIMEOUT;

            return $timeToReset > 0 ? $timeToReset : 0;
        } catch (ValidationException $exception) {
            return 0;
        }
    }

    /**
     * Generate verification token, stored it to reset password table
     * and send user notification with one
     *
     * @param User $user
     *
     * @return integer
     */
    protected function saveResetRequest($user)
    {
        $verificationCode = rand(100000, 999999);

        $resetData = PasswordReset::create([
            'phone'      => $user->phone,
            'expired_at' => Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT),
            'token'      => Hash::make($verificationCode)
        ]);

        if ($resetData) {
            $user->sendSms(__('sms.verification', ['code' => $verificationCode]));

            return User::RESET_TOKEN_TIMEOUT;
        }

        return 0; // allow user send reset request if occasionally something went wrong
    }

    /**
     * Retrieve the latest reset data by phone number
     *
     * @param string $phone
     *
     * @return PasswordReset
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function fetchResetDate(string $phone)
    {
        $lastResetData = PasswordReset::where('phone', $phone)->where('expired_at', '>', DB::raw('NOW()'))->orderBy('expired_at', 'desc')->first();

        if ($lastResetData)
            return $lastResetData;

        throw ValidationException::withMessages(['phone' => [__('passwords.token_not_found')]]);
    }

    /**
     * Check whether token much with hash
     *
     * @param string $token
     * @param string $hash
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function checkToken(string $token, string $hash)
    {
        if (!Hash::check($token, $hash)) {
            throw ValidationException::withMessages(['token' => [__('passwords.token')]]);
        }
    }

    /**
     * Reset user password
     *
     * @param \App\Models\User $user
     * @param string           $password
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function resetPassword(User $user, string $password)
    {
        $user->password = Hash::make($password);

        // obviously if user reach this step he confirmed his phone number
        $user->removeFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION);

        if ($user->save()) {
            event(new PasswordResetEvent($user));
        }
        else {
            throw ValidationException::withMessages(['phone' => [__('passwords.reset_failed')]]);
        }
    }

}
