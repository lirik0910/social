<?php

namespace App\GraphQL\Mutations\Admin\ProfilesBackground;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\ProfilesBackground\UploadProfilesBackgroundsRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UploadProfilesBackgrounds
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param UploadProfilesBackgroundsRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws \ImagickException
     */
    protected function resolve($rootValue, UploadProfilesBackgroundsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('background', $user);

        $files = Arr::get($args->validated(), 'files');

        $backgrounds = [];

        foreach ($files as $file) {
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND);

            $image_info = MediaHelper::uploadAdminImage($file, $s3path);

            $background = Arr::only($image_info, ['name', 'mimetype', 'size']);
            $background['created_at'] = Carbon::now();
            $background['updated_at'] = Carbon::now();

            array_push($backgrounds, $background);

            MediaHelper::makeAndUploadAdminThumbnail($file, $s3path, $background, 'background');
        }

        $backgrounds_count = count($backgrounds);

        if ($backgrounds_count > 0 && !ProfilesBackground::insert($backgrounds)) {
            throw new GraphQLSaveDataException(__('media.profile_background_create_failed'), __('Error'));
        }

        return ProfilesBackground
            ::limit($backgrounds_count)
            ->orderByDesc('created_at')
            ->get();
    }
}
