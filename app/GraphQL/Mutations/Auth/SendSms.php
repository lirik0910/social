<?php

namespace App\GraphQL\Mutations\Auth;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\PhoneHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SendSms
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();
        $timeToReset = $user->time_to_reset;

        if ($timeToReset <= 0) {
            // TODO: remove checking on test environment
            $isTestEnvironment = env('APP_ENV') != "production";

            $verificationCode = $isTestEnvironment ? config('app.phone_verification_default_code') : PhoneHelper::generateVerificationCode();

            $user->phone_verification_code = Hash::make($verificationCode);
            $user->phone_verification_expired_at = Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT);
            if ($user->save()) {
                if (!$isTestEnvironment) {
                    $user->sendSms(__('sms.verification', ['code' => $verificationCode]));
                }

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
}
