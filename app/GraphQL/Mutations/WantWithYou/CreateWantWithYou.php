<?php

namespace App\GraphQL\Mutations\WantWithYou;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\User\WantWithYou\CreateWantWithYouRequest;
use App\Models\User;
use App\Models\WantWithYou;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateWantWithYou
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateWantWithYouRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return WantWithYou
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateWantWithYouRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $another_user_id = Arr::get($inputs, 'user_id');
        $type = Arr::get($inputs, 'type');

        $who_want = $context->user();

        $another_user = User
            ::whereId($another_user_id)
            ->firstOrFail();

        // Check action`s availability to this user
        $another_user->isBlocked();

        $want_with_you = new WantWithYou();
        $want_with_you->user_id = $another_user_id;
        $want_with_you->who_want_id = $who_want->id;
        $want_with_you->type = $type;

        if(!$want_with_you->save()) {
            throw new GraphQLSaveDataException(__('want_with_you.create_failed'), __('Error'));
        }

        return $want_with_you;
    }
}
