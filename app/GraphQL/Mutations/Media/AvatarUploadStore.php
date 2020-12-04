<?php

namespace App\GraphQL\Mutations\Media;

use App\Events\Media\AvatarUploaded;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Media\AvatarUploadStoreRequest;
use App\Models\Media;
use App\Helpers\MediaHelper;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AvatarUploadStore
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Uploaded avatar
     *
     * @var Media
     */
    protected $avatar;

    /**
     * @param $rootValue
     * @param AvatarUploadStoreRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLSaveDataException
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, AvatarUploadStoreRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_AVATAR);

        if (!MediaHelper::checkExists($s3path . '/' . $user->id . '/' . $inputs['name'])) {
            throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
        }

        $media = new Media();
        $media->user_id = $user->id;
        $media->type = Media::TYPE_AVATAR;

        $media->fill($inputs);

        if (!$media->save()) {
            throw new GraphQLSaveDataException(__('media.no_files_were_sent'), __('Error'));
        }

        $user->image = $media->name;

        // User will save in event listener (ChangeUserVerifyingFlags)
        event(new AvatarUploaded($user, $media));

        return [
            'user' => $user,
            'avatar' => $media
        ];
    }
}
