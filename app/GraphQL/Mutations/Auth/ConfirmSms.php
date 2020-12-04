<?php

namespace App\GraphQL\Mutations\Auth;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Auth\ConfirmSmsRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use GraphQL\Type\Definition\ResolveInfo;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ConfirmSms
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ConfirmSmsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws ValidationException
     */
    protected function resolve($rootValue, ConfirmSmsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        $code = Arr::get($args->validated(), 'code');

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
}
