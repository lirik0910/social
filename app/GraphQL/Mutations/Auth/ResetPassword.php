<?php

namespace App\GraphQL\Mutations\Auth;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Traits\DynamicValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ResetPassword
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param ResetPasswordRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, ResetPasswordRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        try {
            $user = User::getUserByPhone($inputs['phone']);

            if (!$user) {
                throw new GraphQLValidationException(['phone' => [__('user.cannot_find_by_phone')]], __('Input validation failed.'));
            }

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
