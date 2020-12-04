<?php

namespace App\GraphQL\Mutations\Admin\CharityOrganization;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\CharityOrganization\CreateCharityOrganizationRequest;
use App\Models\CharityOrganization;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateCharityOrganization
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  CreateCharityOrganizationRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateCharityOrganizationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('charity', $user);

        $inputs = $args->validated();

        $image = Arr::pull($inputs, 'image');

        $charity_organization = new CharityOrganization();

        $charity_organization->fill($inputs);

        if (!empty($image)) {
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_CHARITY_ORGANIZATION_IMAGE);

            $image = MediaHelper::uploadAdminImage($image, $s3path)['name'];

            $charity_organization->image = $image;
        }

        if (!$charity_organization->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.create_failed'), __('Error!'));
        }

        return $charity_organization;
    }
}
