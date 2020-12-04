<?php

namespace App\GraphQL\Mutations\Profile;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Profile\CustomBackgroundUploadStoreRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use App\Helpers\MediaHelper;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CustomBackgroundUploadStore
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CustomBackgroundUploadStoreRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CustomBackgroundUploadStoreRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND);

        if (!MediaHelper::checkExists($s3path . '/users/' . $inputs['name'])) {
            throw new GraphQLlogicRestrictException(__('media.background_file_not_exists'), __('Error'));
        }

        $profile_background = ProfilesBackground::where('user_id', $user->id)->first();

        if($profile_background) {
            $profile_background_path = $s3path . '/users/'  . $profile_background->name;

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

        $profile = $user->profile;
        $profile->profile_background = $profile_background->name;

        if (!$profile_background->save() || !$profile->save()) {
            throw new GraphQLSaveDataException(__('media.profile_background_create_failed'), __('Error'));
        }


        return [
            'user' => $user,
            'media' => $profile_background
        ];
    }
}
