<?php

namespace App\GraphQL\Mutations\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Advert\CreateAdvertRequest;
use App\Models\Advert;
use App\Models\User;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateAdvert
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateAdvertRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Advert
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateAdvertRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        if (!$user->image) {
            throw new GraphQLLogicRestrictException(__('advert.avatar_not_exists'), __('Error'));
        }

        if ($user->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
            throw new GraphQLLogicRestrictException(__('advert.cannot_create_for_private'), __('Error!'));
        }

        $current_adverts_count = Advert
            ::where('user_id', $user->id)
            ->whereDoesntHave('meeting')
            ->whereNull('cancelled_at')
            ->whereNull('respond_id')
            ->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()])
            ->count();

        if ($current_adverts_count > 1) {
            throw new GraphQLLogicRestrictException(__('advert.daily_max_count'), __('Error'));
        }

        $advert = new Advert();
        $advert->user_id = $user->id;
        $advert->end_at = Carbon::now()->addDay();

        $advert->fill($inputs);

        if ($advert->type == Advert::TYPE_BUY && $advert->safe_deal_only && $advert->price > $user->balance) {
            throw new GraphQLSaveDataException(__('advert.insufficient_funds_in_the_account'), __('Error'));
        }

        if (!$advert->save()) {
            throw new GraphQLSaveDataException(__('advert.create_failed'), __('Error'));
        }

        return $advert;
    }
}
