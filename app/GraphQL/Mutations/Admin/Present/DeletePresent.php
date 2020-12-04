<?php

namespace App\GraphQL\Mutations\Admin\Present;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Present;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeletePresent
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
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('present', $user);

        $id = Arr::get($args->validated(), 'id');

        $present = Present::whereId($id)->firstOrFail();

        if (!$present->delete()) {
            throw new GraphQLSaveDataException(__('present.delete_failed'), __('Error!'));
        }

        // Presents will not deleted from AWS
        //MediaHelper::deleteMedia(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_IMAGE) . '/' . $present->category_id . '/' . $present->image);

        return true;
    }
}
