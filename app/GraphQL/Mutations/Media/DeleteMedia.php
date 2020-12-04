<?php

namespace App\GraphQL\Mutations\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\MediaHelper;
use App\Http\Requests\Media\DeleteMediaRequest;
use App\Models\Media;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteMedia
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param DeleteMediaRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return boolean
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, DeleteMediaRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $media = Media::whereId($inputs['id'])->firstOrFail();
        $s3path = $this->getS3Path($media->type);

        MediaHelper::deleteMedia($s3path . '/' . $user->id . '/' . $media->name);
        MediaHelper::deleteFolder($s3path . '/' . $user->id . '/' . pathinfo($media->name, PATHINFO_FILENAME));

        if (!$media->delete()) {
            throw new GraphQLSaveDataException(__('media.failed_to_delete'), __('Error'));
        }

        return true;
    }

    /**
     * Get S3 path for delete file
     *
     * @param int $media_type
     * @return int
     * @throws GraphQLLogicRestrictException
     */
    public function getS3Path(int $media_type)
    {
        switch ($media_type) {
            case Media::TYPE_IMAGE:
                $type = MediaHelper::FILE_TYPE_MEDIA_IMAGE;
                break;
            case Media::TYPE_VIDEO:
                $type = MediaHelper::FILE_TYPE_MEDIA_VIDEO;
                break;
            case Media::TYPE_AVATAR:
                $type = MediaHelper::FILE_TYPE_MEDIA_AVATAR;
                break;
            default:
                throw new GraphQLLogicRestrictException(__('media.unknown_media_type'), __('Error'));
                break;
        }

        return MediaHelper::getS3Path($type);
    }
}
