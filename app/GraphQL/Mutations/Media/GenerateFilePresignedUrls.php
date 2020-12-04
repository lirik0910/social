<?php

namespace App\GraphQL\Mutations\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Http\Requests\Media\GenerateFilePresignedUrlsRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GenerateFilePresignedUrls
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
     * @param GenerateFilePresignedUrlsRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, GenerateFilePresignedUrlsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();
        $this->user = $context->user();

        $s3path = $this->getFullS3Path($inputs['type']);

        $results = [];
        $i = 1;
        foreach ($inputs['files'] as $file) {
            $presignedMediaUrl = MediaHelper::createPresignedUrl($inputs['type'], $file, $s3path);
            $results[$i] = [
                'rname' => $file['name'],
                'name' => $presignedMediaUrl['name'],
                'uri' => $presignedMediaUrl['uri'],
                'description' => $file['description'],
                'mimetype' => $file['mimetype'],
            ];

            if (!empty($file['thumbs'])) {
                $results[$i]['thumbs'] = MediaHelper::createThumbnailPresignedUrl($inputs['type'], $file, $s3path, $presignedMediaUrl['name']);
            }

            $i++;
        }

        return $results;
    }

    /**
     * Return AWS full path
     *
     * @param $type
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    protected function getFullS3Path($type)
    {
        $root_path = MediaHelper::getS3Path($type);

        if ($type === MediaHelper::FILE_TYPE_PROFILE_BACKGROUND) {
            $full_path = $root_path . '/users';
        } else {
            $full_path = $root_path . '/' . $this->user->id;
        }

        return $full_path;
    }
}
