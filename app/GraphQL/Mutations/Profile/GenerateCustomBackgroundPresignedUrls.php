<?php

namespace App\GraphQL\Mutations\Profile;

use App\Http\Requests\Profile\GenerateCustomBackgroundPresignedUrlsRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use App\Helpers\MediaHelper;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GenerateCustomBackgroundPresignedUrls
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param GenerateCustomBackgroundPresignedUrlsRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    protected function resolve($rootValue, GenerateCustomBackgroundPresignedUrlsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $s3path = ProfilesBackground::BUCKET_ROOT_PATH;

        $presignedMediaUrl = MediaHelper::createPresignedUrl(0, $inputs, $s3path . '/users');
        $presignedThumbnailUrl = MediaHelper::createThumbnailPresignedUrl(0, $inputs, $s3path . '/users', $presignedMediaUrl['name']);

        return [
            'rname' => $inputs['name'],
            'name' => $presignedMediaUrl['name'],
            'uri' => $presignedMediaUrl['uri'],
            'mimetype' => $inputs['mimetype'],
            'thumbs' => $presignedThumbnailUrl,
        ];
    }
}
