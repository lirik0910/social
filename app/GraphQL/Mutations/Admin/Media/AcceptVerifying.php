<?php

namespace App\GraphQL\Mutations\Admin\Media;

use App\Events\Media\AvatarVerifyingApprove;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Media\AcceptVerifyingRequest;
use App\Models\Media;
use App\Models\UserPhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AcceptVerifying
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param AcceptVerifyingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, AcceptVerifyingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('verification', $user);

        $inputs = $args->validated();

        $media = Media::where(['id' => $inputs['id'], 'type' => Media::TYPE_AVATAR, 'status' => Media::STATUS_VERIFYING_PENDING])->firstOrFail();
        $media->status = Media::STATUS_VERIFIED;

        $verification_request = UserPhotoVerification::where(['media_id' => $media->id, 'status' => UserPhotoVerification::STATUS_PENDING])->firstOrFail();
        $verification_request->status = UserPhotoVerification::STATUS_ACCEPTED;

        if (!$media->save()) {
            throw new GraphQLSaveDataException('media.update_failed', __('Error'));
        } elseif (!$verification_request->save()) {
            throw new GraphQLSaveDataException('media.verification_request_update_failed', __('Error'));
        }

        event(new AvatarVerifyingApprove($media->user, $media));

        return [
            'media' => $media,
            'verification_request' => $verification_request,
        ];
    }
}
