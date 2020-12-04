<?php

namespace App\GraphQL\Mutations\Admin\VerificationSign;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\PhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeleteVerificationSign
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
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('verification', $user);

        $id = Arr::get($args->validated(), 'id');

        $sign = PhotoVerification::whereId($id)->firstOrFail();

        if (!$sign->delete()) {
            throw new GraphQLSaveDataException(__('media.verification_sign_delete_failed'), __('Error!'));
        }

        MediaHelper::deleteMedia(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION_SIGN) . '/' . $sign->name);

        return true;
    }
}
