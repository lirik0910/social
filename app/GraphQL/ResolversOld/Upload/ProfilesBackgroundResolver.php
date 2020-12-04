<?php


namespace App\GraphQL\ResolversOld\Upload;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\MediaHelper;
use App\Models\ProfilesBackground;
use App\Traits\RequestDataValidate;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class ProfilesBackgroundResolver
{
    use RequestDataValidate;

    /**
     * @var string
     */
    protected $s3path = ProfilesBackground::BUCKET_ROOT_PATH;

    /**
     * Generate pre signed urls for custom (uploaded by user) profiles background
     *
     * @param $rootValue
     * @param array $args
     * @return array
     * @throws GraphQLValidationException
     */
    public function resolveCustomUploadGenerate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = \Auth::user();

        $presignedMediaUrl = MediaHelper::createPresignedUrl(0, $inputs, $this->s3path . '/users');
        $presignedThumbnailUrl = MediaHelper::createThumbnailPresignedUrl(0, $inputs, $this->s3path . '/users', $presignedMediaUrl['name']);

        $results = [
            'name' => $presignedMediaUrl['name'],
            'uri' => $presignedMediaUrl['uri'],
            'mimetype' => $inputs['mimetype'],
            'size' => $inputs['size'],
            'thumbs' => $presignedThumbnailUrl,
        ];

        return [
            'status' => 'URL(s) successfully generated',
            'results' => $results,
        ];
    }

    /**
     * Store into DB uploaded custom (uploaded by user) profiles background
     *
     * @param $rootValue
     * @param array $args
     * @return ProfilesBackground
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCustomUploadStore($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = \Auth::user();

        if (!MediaHelper::checkExists($this->s3path . '/users/' . $inputs['name'])) {
            throw new GraphQLlogicRestrictException(__('media.background_file_not_exists'), __('Error'));
        }

        $profile_background = ProfilesBackground::where('user_id', $user->id)->firstOrFail();

        if($profile_background) {
            $profile_background_path = $this->s3path . '/users/'  . $profile_background->name;

            if(MediaHelper::checkExists($profile_background_path)) {
                if(!MediaHelper::deleteMedia($profile_background_path)) {
                    throw new GraphQLLogicRestrictException(__('media.profile_background_delete_failed'), __('Error'));
                }
            }
        } else {
            $profile_background = new ProfilesBackground();
            $profile_background->user_id = $user->id;
        }

        $profile_background->fill($inputs);

        if (!$profile_background->save()) {
            throw new GraphQLSaveDataException(__('media.profile_background_create_failed'), __('Error'));
        }

        return $profile_background;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string',
            'mimetype' => 'required|string',
            'size' => 'integer|max:'. ProfilesBackground::MAX_SIZE,
            'thumbs' => 'nullable|array|min:1'
        ];
    }
}
