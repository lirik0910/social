<?php

namespace App\GraphQL\Mutations\Admin\ServicePage;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\ServicePage\UpdateServicePageRequest;
use App\Models\ServicePage;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateServicePage
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param UpdateServicePageRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    protected function resolve($rootValue, UpdateServicePageRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('service_pages', $user);

        $inputs = $args->validated();

        $id = Arr::pull($inputs, 'id');

        $page = ServicePage
            ::whereId($id)
            ->firstOrFail();

        if (isset($inputs['slug'])) {
            $inputs['slug'] = mb_strtolower($page->slug);

            if (ServicePage::where('slug', '=', $inputs['slug'])->where('id', '!=', $page->id)->exists()) {
                throw new GraphQLValidationException([['slug' => [__('service_page.slug_is_not_unique')]]],__('Input validation failed.'));
            }
        }

        $page->fill($inputs);

        if (!$page->save()) {
            throw new GraphQLSaveDataException(__('static_page.update_failed'), __('Error'));
        }

        return $page;
    }
}
