<?php

namespace App\GraphQL\Mutations\Admin\CharityOrganization;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\CharityOrganization\UpdateNativeCharityRequest;
use App\Models\CharityOrganization;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateNativeCharityOrganization
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param UpdateNativeCharityRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return CharityOrganization
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateNativeCharityRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('charity', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');
        $image = Arr::pull($inputs, 'image');

        $charity_organization = CharityOrganization
            ::whereId($id)
            ->whereNull('user_id')
            ->firstOrFail();

        $charity_organization->fill($inputs);

        if (!empty($image)) {
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_CHARITY_ORGANIZATION_IMAGE);

            $image = MediaHelper::uploadAdminImage($image, $s3path)['name'];

            if (MediaHelper::checkExists($s3path . '/' . $image)) {
                MediaHelper::deleteMedia($s3path . '/' . $charity_organization->image);

                $charity_organization->image = $image;
            }
        }

        if (!$charity_organization->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.update_failed'), __('Error'));
        }

        return $charity_organization;
    }
}
