<?php

namespace App\GraphQL\Queries\Meeting;

use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Meeting as Model;

class Meeting
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
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        if ($user->role !== User::ROLE_USER) {
            AdminPermissionsHelper::check('meeting_info', $user);
        }

        $id = Arr::get($args->validated(), 'id');

        return Model
            ::whereId($id)
            ->firstOrFail();
    }
}
