<?php

namespace App\GraphQL\Queries\Admin\ServicePage;

use App\Http\Requests\Admin\ServicePage\ServicePagesRequest;
use App\Models\ServicePage;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ServicePages
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ServicePagesRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, ServicePagesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $locale = Arr::get($args->validated(), 'locale');

        return ServicePage
            ::where('locale', $locale)
            ->get();
    }
}
