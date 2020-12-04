<?php

namespace App\GraphQL\Mutations\Admin\Present;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\Present\CreatePresentRequest;
use App\Models\Present;
use App\Models\PresentCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreatePresents
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  CreatePresentRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, CreatePresentRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('present', $user);

        $inputs = $args->validated();

        $category_id = Arr::get($inputs, 'category_id');

        $category = PresentCategory
            ::whereId($category_id)
            ->firstOrFail();

        $presents = [];

        foreach ($inputs['presents'] as $present) {
            $image = Arr::pull($present, 'image');

            $new_present = new Present();

            $new_present->fill($present);

            if (!empty($image)) {
                $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_IMAGE) . '/' . $category_id;

                $image_info = MediaHelper::uploadAdminImage($image, $s3path);

                $new_present->image = Arr::get($image_info, 'name');
                $new_present->fill(Arr::only($image_info, ['mimetype', 'size']));
            }

            array_push($presents, $new_present);
        }

        if (count($presents) > 0) {
            $presents = $category
                ->presents()
                ->saveMany($presents);
        }

        return $presents;
    }
}
