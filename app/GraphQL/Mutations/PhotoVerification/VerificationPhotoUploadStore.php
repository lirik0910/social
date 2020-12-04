<?php

namespace App\GraphQL\Mutations\PhotoVerification;

use App\Events\Media\AvatarVerifyingRequest;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\MediaHelper;
use App\Http\Requests\PhotoVerification\VerificationPhotoUploadStoreRequest;
use App\Models\Media;
use App\Models\UserPhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VerificationPhotoUploadStore
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param VerificationPhotoUploadStoreRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, VerificationPhotoUploadStoreRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION);

        if (!MediaHelper::checkExists($s3path . '/' . $user->id . '/' . $inputs['name'])) {
            throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
        }

        if (!empty($inputs['media_id'])) {
            $media = Media::whereId((int)$inputs['media_id'])->firstOrFail();
        } else {
            $media = Media::where('name', $user->image)->firstOrFail();
        }

        $photo_verification = UserPhotoVerification
            ::where('user_id', $user->id)
            ->where('media_id', $media->id)
            ->where('status', UserPhotoVerification::STATUS_NEW)
            ->firstOrFail();

        $photo_verification->fill($inputs);
        $photo_verification->status = UserPhotoVerification::STATUS_PENDING;
        $media->status = Media::STATUS_VERIFYING_PENDING;

        if (!$photo_verification->save() || !$media->save()) {
            throw new GraphQLSaveDataException(__('user_photo_verification.update_failed'), __('Error'));
        }

        // User will save in event listener (ChangeUserVerifyingFlags)
        event(new AvatarVerifyingRequest($user, $media));

        return [
            'user' => $user,
            'media' => $media
        ];
    }
}
