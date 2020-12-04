<?php

namespace App\GraphQL\Mutations\Advert;

use App\Http\Requests\Advert\CreateAdvertRespondRequest;
use App\Models\AdvertRespond;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Advert;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateAdvertRespond
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateAdvertRespondRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateAdvertRespondRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $advert = Advert::whereId($inputs['advert_id'])->firstOrFail();

        $advert_user = $advert->user;

        // Check action`s availability to this user
        $advert_user->isBlocked();

        if (!$user->can('createRespond', $advert)) {
            throw new GraphQLLogicRestrictException(__('common.permission_denied'), __('Error!'));
        }

        if ($advert->isEnded()) {
            throw new GraphQLLogicRestrictException(__('advert.already_ended'), __('Error'));
        }

        if ($advert->responds()->where('user_id', $user->id)->exists()) {
            throw new GraphQLLogicRestrictException(__('advert.respond_already_exist'), __('Error'));
        }

        $advert_respond = new AdvertRespond();
        $advert_respond->user_id = $user->id;
        $advert_respond->advert_id = $advert->id;
        $advert_respond->advert_user_id = $advert_user->id;

        if ($advert->type == Advert::TYPE_SELL && $advert->safe_deal_only && $advert->price > $user->balance) {
            throw new GraphQLSaveDataException(__('advert.insufficient_funds_in_the_account'), __('Error'));
        }

        if (!$advert_respond->save()) {
            throw new GraphQLSaveDataException(__('advert.create_failed'), __('Error'));
        }

        $advert->increment('participants');

        return $advert;
    }
}
