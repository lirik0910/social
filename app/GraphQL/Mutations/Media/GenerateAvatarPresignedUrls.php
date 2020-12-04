<?php

namespace App\GraphQL\Mutations\Media;

use App\Http\Requests\Media\GenerateAvatarPresignedUrlsRequest;
use App\Models\Media;
use App\Helpers\MediaHelper;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GenerateAvatarPresignedUrls
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, GenerateAvatarPresignedUrlsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path(Media::TYPE_AVATAR);
        $presignedMediaUrl = MediaHelper::createPresignedUrl(Media::TYPE_AVATAR, $inputs, $s3path . '/' . $user->id);
        $presignedThumbnailUrl = MediaHelper::createThumbnailPresignedUrl(Media::TYPE_AVATAR, $inputs, $s3path . '/' . $user->id, $presignedMediaUrl['name']);

        $results = [
            'name' => $presignedMediaUrl['name'],
            'uri' => $presignedMediaUrl['uri'],
            'mimetype' => $inputs['mimetype'],
            'size' => $inputs['size'],
            'thumbs' => $presignedThumbnailUrl,
        ];

        return $results;
    }
}
