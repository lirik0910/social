<?php

namespace App\GraphQL\Mutations\Admin\Present;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\Present\UpdatePresentCategoryRequest;
use App\Models\PresentCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdatePresentCategory
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UpdatePresentCategoryRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdatePresentCategoryRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('present', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');

        $image = Arr::pull($inputs, 'image');

        $present_category = PresentCategory::whereId($id)->firstOrFail();

        $present_category->fill($inputs);

        if (!empty($image)) {
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_CATEGORY_IMAGE);

            $image_info = MediaHelper::uploadAdminImage($image, $s3path);

            if (MediaHelper::checkExists($s3path . '/' . $image_info['name'])) {
                MediaHelper::deleteMedia($s3path . '/' . $present_category->image);

                $present_category->image = $image_info['name'];
                $present_category->size = $image_info['size'];
                $present_category->mimetype = $image_info['mimetype'];
            }
        }

        if(!$present_category->save()) {
            throw new GraphQLSaveDataException(__('present.category_save_failed'), __('Error!'));
        }

        return $present_category;
    }
}
