<?php

namespace App\GraphQL\Mutations\PhotoVerification;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Http\Requests\PhotoVerification\GenerateVerificationPhotoPresignedUrlsRequest;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GenerateVerificationPhotoPresignedUrls
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param GenerateVerificationPhotoPresignedUrlsRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, GenerateVerificationPhotoPresignedUrlsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION);
        $presignedUrl = MediaHelper::createPresignedUrl(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION, $inputs, $s3path . '/' . $user->id);

        return [
            'rname' => $inputs['name'],
            'name' => $presignedUrl['name'],
            'mimetype' => $inputs['mimetype'],
            'uri' => $presignedUrl['uri'],
        ];
    }
}
