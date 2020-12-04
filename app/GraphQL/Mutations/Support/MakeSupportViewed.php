<?php

namespace App\GraphQL\Mutations\Support;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Support;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class MakeSupportViewed
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
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        $id = Arr::get($args->validated(), 'id');

        $support = Support
            ::whereId($id)
            ->firstOrFail();

        if ($user->id === $support->user_id) {
            $support->unviewed_by_user = false;
        } elseif ($user->id === $support->moderator_id) {
            $support->unviewed_by_moderator = false;
        }

        if ($support->isDirty()) {
            if (!$support->save()) {
                throw new GraphQLSaveDataException(__('support.update_failed'), __('Error'));
            }
        }

        return $support;
    }
}
