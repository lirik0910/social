<?php

namespace App\GraphQL\Mutations\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Advert\CancelAdvertRespondRequest;
use App\Models\Advert;
use App\Models\AdvertRespond;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CancelAdvertRespond
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CancelAdvertRespondRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CancelAdvertRespondRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $advert = Advert::whereId($inputs['advert_id'])->firstOrFail();

        if ($advert->isEnded()) {
            throw new GraphQLLogicRestrictException(__('advert.already_ended'), __('Error'));
        }

        $respond = AdvertRespond
            ::where([
                'user_id' => $user->id,
                'advert_id' => $advert->id
            ])
            ->firstOrFail();

        if ($user->id !== $respond->user_id) {
            throw new GraphQLLogicRestrictException('common.permission_denied', 'Error!');
        }

        if (!$respond->delete()) {
            throw new GraphQLSaveDataException(__('advert.respond_delete_failed'), __('Error'));
        }

        return $respond;
    }
}
