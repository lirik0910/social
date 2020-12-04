<?php

namespace App\GraphQL\Mutations\Admin\CharityOrganization;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\CharityOrganization\DeclineCustomCharityOrganizationRequest;
use App\Models\CharityOrganization;
use App\Notifications\CharityOrganization\CharityOrganizationModerationResult;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeclineCustomCharityOrganization
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  DeclineCustomCharityOrganizationRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, DeclineCustomCharityOrganizationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('charity', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');

        $charity = CharityOrganization
            ::whereId($id)
            ->where('moderation_status', '=', CharityOrganization::MODERATION_STATUS_PENDING)
            ->firstOrFail();

        $charity->moderation_status = CharityOrganization::MODERATION_STATUS_DECLINED;
        $charity->moderation_declined_reason = Arr::get($inputs, 'moderation_declined_reason');
        $charity->available = false;

        if (!$charity->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.update_failed'), __('Error!'));
        }

        $charity->user->notify(new CharityOrganizationModerationResult($charity));

        return $charity;
    }
}
