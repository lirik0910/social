<?php

namespace App\GraphQl\ResolversOld\PhotoVerification;

use App\Events\PhotoVerified;
use App\Traits\RequestDataValidate;
use App\Exceptions\GraphQLSaveDataException;
use Illuminate\Validation\ValidationException;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

use App\Models\UserPhotoVerification;
use Carbon\Carbon;

use App\Helpers\MediaHelper;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PhotoVerification
{
    use RequestDataValidate;

    const MEDIA_SIZE = 10485760;

    /**
     * Generate verification photo
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveGeneratePhoto($rootValue, array $args)
    {
        $user = \Auth::user();

        $openVerifications = UserPhotoVerification::where('user_id', $user->id)->where('verification_expired_at', '>', new \DateTime())->first();
        if (!$openVerifications) {
            $time = Carbon::now()->addSeconds(UserPhotoVerification::EXPIRED_PHOTO_VERIFICATION_TIMEOUT);

            $image = \App\Models\PhotoVerification::all()->random(1)->first();
            $user->photoVerification()->create([
                'verification_photo_id' => $image->id,
                'verification_expired_at' => $time,
            ]);
            $uri = $image->media_uri;
            $time_to_end = Carbon::now()->diffInSeconds($time, false);
        } else {
            $uri = $openVerifications->photo->media_uri;
            $time_to_end = Carbon::now()->diffInSeconds($openVerifications->verification_expired_at, false);
        }
        return [
            'uri' => $uri,
            'time' => $time_to_end
        ];
    }

    /**
     * Generate presign uri for verification user photo
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolvePresignUriVerificationPhoto($rootValue, array $args)
    {
        $input = $args['data'];
        try {
            $input = $this->validatedData($input);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $PresignedUrl = MediaHelper::createPresignedUrl($input, Media::BUCKET_ROOT_PATH);
        return [
            'name' => $PresignedUrl['name'],
            'uri' => $PresignedUrl['uri'],
        ];
    }

    /**
     * Store verification user photo
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveStoreVerificationPhoto($rootValue, array $args)
    {
        $user = Auth::user();
        $input = $args['data'];

        try {
            $input = $this->validatedData($input);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }
        if (!MediaHelper::checkExists(Media::BUCKET_ROOT_PATH . '/' . $user->id . '/' . $input['name']))
            throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));

        $verified_photo = $user->photoVerifications()->create([
            'name' => $input['name'],
            'mimetype' => $input['mimetype'],
            'size' => $input['size'],
        ]);

        /** Send notification to user about photo verified **/
        event(new PhotoVerified($user->photoVerification, $user));

        return [
            'status' => 'File saved successfully'
        ];
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer',
            'name' => 'required|string',
            'mimetype' => 'required|string',
            'size' => 'integer|max:'. self::MEDIA_SIZE,
        ];
    }
}
