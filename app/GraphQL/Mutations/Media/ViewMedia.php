<?php

namespace App\GraphQL\Mutations\Media;

use App\Http\Requests\Media\ViewMediaRequest;
use App\Models\Media;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ViewMedia
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ViewMediaRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     */
    protected function resolve($rootValue, ViewMediaRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $media = Media::whereId($inputs['id'])->firstOrFail();

        if ($user->id !== $media->user_id) {
            $viewExists = $media->users_views()->where('user_id', $user->id)->exists();

            if (!$viewExists) {
                $media->users_views()->create(['user_id' => $user->id]);
                $views_count = $media->increment('views');
            }
        }

        return $views_count ?? $media->views;
    }
}
