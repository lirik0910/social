<?php

namespace App\GraphQL\Mutations\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Advert\ApproveAdvertRequest;
use App\Models\Advert;
use App\Models\AdvertRespond;
use App\Models\Meeting;
use App\Models\UserMeetingsOption;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ApproveAdvertRespond
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ApproveAdvertRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ApproveAdvertRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $auth_user = Auth::user();

        $advert = Advert
            ::whereId($inputs['advert_id'])
            ->firstOrFail();

        if (!$auth_user->can('approveRespond', $advert)) {
            throw new GraphQLLogicRestrictException('common.permission_denied', __('Error!'));
        }

        if ($advert->isEnded()) {
            throw new GraphQLLogicRestrictException(__('advert.already_ended'), __('Error'));
        }

        $respond = AdvertRespond
            ::where([
                'advert_id' => $inputs['advert_id'],
                'user_id' => $inputs['user_id']
            ])
            ->firstOrFail();

        if($advert->type === Advert::TYPE_BUY) {
            $meetings_options_user_id = $respond->user_id;
        } else {
            $meetings_options_user_id = $advert->user_id;
        }

        $charity_organization_id = UserMeetingsOption
            ::where('user_id', $meetings_options_user_id)
            ->firstOrFail()
            ->charity_organization_id;


        $advert->respond_id = $respond->id;
        $advert->respond_user_id = $respond->user_id;
        $advert->charity_organization_id = $charity_organization_id;

        if (!$advert->save()) {
            throw new GraphQLSaveDataException(__('advert.update_failed'), __('Error'));
        }

        $this->createMeeting($advert, $inputs['user_id']);

        return $advert;
    }

    /**
     * @param $advert
     * @param $responded_user_id
     * @throws GraphQLLogicRestrictException
     */
    protected function createMeeting($advert, $responded_user_id)
    {
        $auth_user = Auth::user();

        $seller_id = $advert->type === Advert::TYPE_BUY ? $responded_user_id : $auth_user->id;
        $user_id = $seller_id === $auth_user->id ? $responded_user_id : $auth_user->id;

        $meeting_info = clone $advert;
        $meeting_info->seller_id = $seller_id;
        $meeting_info->user_id = $user_id;
        $meeting_info->safe_deal = true;
        $meeting_info->status = Meeting::STATUS_ACCEPTED;

        $meeting = new Meeting($meeting_info->toArray());
        if (!$advert->meeting()->save($meeting)) {
            throw new GraphQLLogicRestrictException(__('advert.update_failed'), __('Error'));
        }
    }
}
