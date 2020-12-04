<?php

namespace App\GraphQL\Mutations\PhotoVerification;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\PhotoVerification\GenerateVerificationPhotoRequest;
use App\Models\Media;
use App\Models\User;
use App\Models\UserPhotoVerification;
use App\Models\PhotoVerification;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GenerateVerificationPhoto
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
     * @param GenerateVerificationPhotoRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, GenerateVerificationPhotoRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $media = $this->getMedia($inputs['media_id']);

        if ($media->status !== Media::STATUS_NOT_VERIFIED) {
            throw new GraphQLLogicRestrictException(__('photo_verification.incorrect_media_status'), __('Error'));
        }

        $openVerifications = UserPhotoVerification
            ::where('user_id', $this->user->id)
            ->where('media_id', $media->id)
            ->where('verification_expired_at', '>', new \DateTime())
            ->with('verification_sign')
            ->where('status', UserPhotoVerification::STATUS_NEW)
            ->first();

        if ($openVerifications) {
            $uri = $openVerifications->verification_sign->image_url;
            $expired_at = $openVerifications->verification_expired_at;
        } else {
            $expired_at = Carbon::now()->addSeconds(UserPhotoVerification::EXPIRED_PHOTO_VERIFICATION_TIMEOUT);

            $sign = PhotoVerification
                ::inRandomOrder()
                ->where('available', true)
                ->firstOrFail();

            $uri = $sign->image_url;

            $user_photo_verification = new UserPhotoVerification();
            $user_photo_verification->media_id = $media->id;
            $user_photo_verification->user_id = $this->user->id;
            $user_photo_verification->verification_photo_id = $sign->id;
            $user_photo_verification->verification_expired_at = $expired_at;

            if (!$user_photo_verification->save()) {
                throw new GraphQLSaveDataException(__('user.photo_verification_create_failed'), __('Error'));
            }
        }

        return [
            'uri' => $uri,
            'expired_at' => $expired_at
        ];
    }

    /**
     * Get verifying media
     *
     * @param $id
     * @return mixed
     */
    protected function getMedia($id)
    {
        $query = Media
            ::where('type', Media::TYPE_AVATAR);

        if ($id) {
            $query->whereId($id);
        } else {
            $query->where('name', $this->user->image);
        }

        return $query->firstOrFail();
    }
}
