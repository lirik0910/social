<?php

namespace App\GraphQL\Mutations\MediaPresent;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\MediaPresent\CreateMediaPresentRequest;
use App\Models\Media;
use App\Models\MediaPresent;
use App\Models\Present;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateMediaPresent
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateMediaPresentRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return object
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateMediaPresentRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $present = Present::whereId($inputs['present_id'])->firstOrFail();
        $media = Media::whereId($inputs['media_id'])->firstOrfail();

        // Check action`s availability to this user
        $media->user->isBlocked();

        if ($present->price > $user->balance) {
            throw new GraphQLLogicRestrictException(__('auction.not_enough_money_in_the_account'), __('Error'));
        }

        $media_present = new MediaPresent();
        $media_present->user_id = $user->id;
        $media_present->price = $present->price;
        $media_present->image_url = $present->image_uri;

        $media_present->fill($inputs);

        $media->presents_cost += $present->price;

        if (!$media_present->save() || !$media->save()) {
            throw new GraphQLSaveDataException(__('media_present.create_failed'), __('Error'));
        }

        return $media_present;
    }
}
