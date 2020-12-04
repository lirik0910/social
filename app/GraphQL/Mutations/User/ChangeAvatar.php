<?php

namespace App\GraphQL\Mutations\User;

use App\Events\User\AvatarChanged;
use App\Http\Requests\User\ChangeAvatarRequest;
use App\Models\Media;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeAvatar
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ChangeAvatarRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return \Illuminate\Foundation\Auth\User|null
     */
    protected function resolve($rootValue, ChangeAvatarRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $media = Media
            ::whereId($inputs['id'])
            ->where('user_id', $user->id)
            ->where('type', Media::TYPE_AVATAR)
            ->where(function ($q) {
                $q->orWhere('status', '!=', Media::STATUS_BANNED);
                $q->orWhereNull('status');
            })
            ->firstOrFail();

        $user->image = $media->name;

        // User will save in event listener (ChangeUserVerifyingFlags)
        event(new AvatarChanged($user, $media));

        return $user;
    }
}
