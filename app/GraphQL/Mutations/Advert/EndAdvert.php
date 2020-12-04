<?php

namespace App\GraphQL\Mutations\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Advert\EndAdvertRequest;
use App\Models\Advert;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EndAdvert
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param EndAdvertRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, EndAdvertRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $advert = Advert::whereId($inputs['id'])->firstOrFail();

        if (!$user->can('cancel', $advert)) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error!'));
        }

        if ($advert->isEnded()) {
            throw new GraphQLLogicRestrictException(__('advert.already_ended'), __('Error'));
        }

        $advert->cancelled_at = Carbon::now();

        if (!$advert->save()) {
            throw new GraphQLSaveDataException(__('advert.update_failed'), __('Error'));
        }

        return $advert;
    }
}
