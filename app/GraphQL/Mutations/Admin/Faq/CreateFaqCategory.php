<?php

namespace App\GraphQL\Mutations\Admin\Faq;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Faq\CreateFaqCategoryRequest;
use App\Models\FaqCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateFaqCategory
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  CreateFaqCategoryRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateFaqCategoryRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('faq', $user);

        $inputs = $args->validated();

        $category = new FaqCategory();

        $category->fill($inputs);

        $category->slug = Str::slug($category->title, '-', $category->locale);

        if (!$category->save()) {
            throw new GraphQLSaveDataException(__('faq.create_failed'), __('Error'));
        }

        return $category;
    }
}
