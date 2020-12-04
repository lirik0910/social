<?php

namespace App\GraphQL\Mutations\Admin\Present;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\PresentCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeletePresentCategory
{
    use DynamicValidation;

    /**
     * @var PresentCategory
     */
    protected $present_category;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param IDRequiredRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws \Exception
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('present', $user);

        $present_category_id = Arr::get($args->validated(), 'id');

        $present_category = PresentCategory::whereId($present_category_id)->firstOrFail();

        \DB::beginTransaction();

        if (!$present_category->delete()) {
            \DB::rollback();

            throw new GraphQLSaveDataException(__('present.category_delete_failed'), __('Error!'));
        } elseif(!$present_category->presents()->delete()) {
            \DB::rollback();

            throw new GraphQLSaveDataException(__('present.delete_failed'), __('Error!'));
        }

        \DB::commit();

        // Presents will not deleted from AWS
        //MediaHelper::deleteMedia(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_CATEGORY_IMAGE) . '/' . $present_category->image);
        //MediaHelper::deleteFolder(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_IMAGE) . '/' . $present_category->id);

        return true;
    }
}
