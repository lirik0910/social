<?php

namespace App\GraphQL\Mutations\Profile;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Models\Profile;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateProfile
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
     * @param CreateProfileRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, CreateProfileRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        if(Profile::where('user_id', $this->user->id)->exists()) {
            throw new GraphQLLogicRestrictException(__('profile.already_exists'), __('Error!'));
        }

        $profile = new Profile();
        $profile->user_id = $this->user->id;

        // store data
        $profile->fill($inputs);
        $this->user->removeFlag(User::FLAG_REQUIRED_FILL_PROFILE);

        // if user change nickname store it to User model
        if (array_key_exists('nickname', $inputs)) {
            $this->user->nickname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $inputs['nickname'])));
        }

        $this->user->slug = $this->user->generateSlug();

        // trying to save changes
        if (!$profile->save()) {
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        } else if (!$this->user->save()) { // failed to change flag
            $profile->delete();
            throw new GraphQLSaveDataException(__('profile.updation_failed'), __('Error'));
        }

        return [
            'user' => $this->user,
            'profile' => $profile,
        ];
    }
}
