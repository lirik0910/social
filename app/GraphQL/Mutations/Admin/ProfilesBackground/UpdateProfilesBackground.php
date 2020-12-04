<?php

namespace App\GraphQL\Mutations\Admin\ProfilesBackground;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Common\UpdateAvailabilityRequest;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateProfilesBackground
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UpdateAvailabilityRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateAvailabilityRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('background', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');
        $availability = Arr::get($inputs, 'available');

        $profiles_background = ProfilesBackground
            ::whereId($id)
            ->firstOrFail();

        $profiles_background->available = $availability;

        if(!$profiles_background->save()) {
            throw new GraphQLSaveDataException(__('verification_sign.update_failed'), __('Error!'));
        }

        return $profiles_background;
    }
}
