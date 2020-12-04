<?php

namespace App\GraphQL\Mutations\Auth;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\PhoneHelper;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\PasswordReset;
use App\Traits\DynamicValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ForgotPassword
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param ForgotPasswordRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, ForgotPasswordRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = User::getUserByPhone($inputs['phone']);

        if (!$user) {
            throw new GraphQLValidationException(['phone' => [__('user.cannot_find_by_phone')]], __('Input validation failed.'));
        }

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
        // TODO: remove checking on test environment
        $isTestEnvironment = env('APP_ENV') != "production";

        $verificationCode = $isTestEnvironment ? config('app.phone_verification_default_code') : PhoneHelper::generateVerificationCode();

        $resetData = PasswordReset::create([
            'phone'      => $user->phone,
            'expired_at' => Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT),
            'token'      => Hash::make($verificationCode)
        ]);

        if ($resetData) {
            if (!$isTestEnvironment) {
                $user->sendSms(__('sms.verification', ['code' => $verificationCode]));
            }

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
}
