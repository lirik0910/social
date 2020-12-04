<?php

namespace App\GraphQL\Mutations\User;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\User\ToggleTwoStepVerificationRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ToggleTwoStepVerification
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ToggleTwoStepVerificationRequest $args
     * @param GraphQLContext $context
     * @return \Illuminate\Foundation\Auth\User|null
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ToggleTwoStepVerificationRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        if ($inputs['two_step_verification']) {
            $user->addFlag(User::FLAG_ENABLED_PHONE_VERIFICATION);
        }
        else {
            $user->removeFlag(User::FLAG_ENABLED_PHONE_VERIFICATION);
        }

        if (!$user->save()) {
            throw new GraphQLSaveDataException(__('Save data failed'), __('Error'));
        }

        return $user;
    }
}
