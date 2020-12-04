<?php

namespace App\GraphQL\Mutations\Media;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Models\Media;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateMedia
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param UpdateMediaRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateMediaRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $media = Media::whereId($inputs['id'])->firstOrFail();

        $media->fill($inputs);

        if (!$media->save()) {
            throw new GraphQLSaveDataException(__('media.update_failed'), __('Error'));
        }

        return $media;
    }

}
