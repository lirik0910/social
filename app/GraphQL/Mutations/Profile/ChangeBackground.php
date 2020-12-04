<?php

namespace App\GraphQL\Mutations\Profile;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Profile\ChangeBackgroundRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeBackground
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ChangeBackgroundRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return \Illuminate\Foundation\Auth\User|null
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ChangeBackgroundRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        if ($inputs['id']) {
            $background = ProfilesBackground
                ::where(function ($query) use ($user) {
                    $query->orWhere('user_id', $user->id);
                    $query->orWhereNull('user_id');
                })
                ->whereId($inputs['id'])
                ->firstOrFail();

            $user->profile->profile_background = $background->name;
        } else {
            $user->profile->profile_background = null;
        }

        if (!$user->profile->save()) {
            throw new GraphQLSaveDataException(__('user.save_failed'), __('Error'));
        }

        return $user;
    }
}
