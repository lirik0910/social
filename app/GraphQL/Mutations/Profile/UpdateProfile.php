<?php

namespace App\GraphQL\Mutations\Profile;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\LanguageHelper;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateProfile
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param UpdateProfileRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateProfileRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();

        $profile = $this->user->profile;

        $inputs = $args->validated();

        // if user change nickname store it to User model
        if (array_key_exists('nickname', $inputs)) {
            $this->user->nickname = trim($inputs['nickname']);
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
        $profile->fill($inputs);

        // trying to save changes
        if (!$this->user->save() || !$profile->save()) {
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        }

        return [
            'user'    => $this->user,
            'profile' => $profile,
        ];
    }
}
