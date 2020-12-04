<?php

namespace App\GraphQL\Mutations\CharityOrganization;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Charity\CustomCharityRequest;
use App\Models\CharityOrganization;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Traits\DynamicValidation;

class UpdateCustomCharityOrganization
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CustomCharityRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return CharityOrganization|mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CustomCharityRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        if ($user->charity_organization) {
            $charity_organization = $user->charity_organization;

            if ($charity_organization->moderation_status === CharityOrganization::MODERATION_STATUS_PENDING) {
                throw new GraphQLLogicRestrictException(__('charity_organization.incorrect_moderation_status'), __('Error'));
            }
        } else {
            $charity_organization = new CharityOrganization();
            $charity_organization->user_id = $user->id;
        }

        $charity_organization->moderation_status = CharityOrganization::MODERATION_STATUS_PENDING;
        $charity_organization->fill($inputs);

        if (!$charity_organization->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.update_failed'), __('Error'));
        } else {
            $meetings_options = $user->meetings_options;

            if (!is_null($meetings_options->charity_organization_id)) {
                $meetings_options->charity_organization_id = null;

                if (!$meetings_options->save()) {
                    throw new GraphQLSaveDataException(__('charity_organization.apply_failed'), __('Error'));
                }
            }
        }

        return $charity_organization;
    }
}
