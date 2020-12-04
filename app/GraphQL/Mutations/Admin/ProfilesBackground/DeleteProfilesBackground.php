<?php

namespace App\GraphQL\Mutations\Admin\ProfilesBackground;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteProfilesBackground
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('background', $user);

        $id = Arr::get($args->validated(), 'id');

        $background = ProfilesBackground
            ::whereId($id)
            ->firstOrFail();

        if (!$background->delete()) {
            throw new GraphQLSaveDataException(__('profile.background_delete_failed'), __('Error!'));
        }

        MediaHelper::deleteMedia(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND) . '/' . $background->name);
        MediaHelper::deleteFolder(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND) . '/' . pathinfo($background->name, PATHINFO_FILENAME));

        return true;
    }
}
