<?php

namespace App\GraphQL\Mutations\Admin\Support;

use App\Events\Support\SupportChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Admin\Support\ChangeSupportCategoryRequest;
use App\Models\Support;
use App\Notifications\Support\SupportCategoryChanged;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeSupportCategory
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ChangeSupportCategoryRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ChangeSupportCategoryRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');
        $category = Arr::get($inputs, 'category');

        $support = Support
            ::whereId($id)
            ->firstOrFail();

        if (!$user->can('changeCategory', $support)) {
            throw new GraphQLLogicRestrictException(__('support.access_denied'), __('Error!'));
        }

        if ($support->status === Support::STATUS_CLOSED) {
            throw new GraphQLLogicRestrictException(__('support.already_ended'), __('Error'));
        }

        $support->category = $category;

        if (!$support->save()) {
            throw new GraphQLSaveDataException(__('support.update_failed'), __('Error'));
        }

        event(new SupportChanged($support, ['category' => $support->category]));

        $support->user->notify(new SupportCategoryChanged($support));

        return $support;
    }
}
