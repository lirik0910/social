<?php

namespace App\GraphQL\Queries\Admin\Present;

use App\Http\Requests\Admin\Present\CategoryPresentsRequest;
use App\Models\Present;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CategoryPresents
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  CategoryPresentsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, CategoryPresentsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $category_id = Arr::get($args->validated(), 'category_id');

        return Present
            ::where('category_id', $category_id)
            ->get();
    }
}
