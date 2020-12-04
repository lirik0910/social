<?php

namespace App\GraphQL\Mutations\Admin\VerificationSign;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Common\UpdateAvailabilityRequest;
use App\Models\PhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateVerificationSign
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
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateAvailabilityRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('verification', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');
        $availability = Arr::get($inputs, 'available');

        $verification_sign = PhotoVerification::whereId($id)->firstOrFail();
        $verification_sign->available = $availability;

        if(!$verification_sign->save()) {
            throw new GraphQLSaveDataException(__('verification_sign.update_failed'), __('Error!'));
        }

        return $verification_sign;
    }
}
