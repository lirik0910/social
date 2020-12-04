<?php

namespace App\GraphQL\Mutations\UserActions;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\BlockHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\BlockedUser;
use App\Models\User;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BlockUserById
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @return integer
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $blocked_user_id = Arr::get($inputs, 'id');

        // EXCLUDE AT AN ATTEMPT TO BLOCK YOURSELF
        if ($user->id == $blocked_user_id) {
            throw new GraphQLLogicRestrictException(__('profile.you_cant_block_yourself'), __('Error'));
        }

        $blocked_user = User::whereId($blocked_user_id)->firstOrFail();
        $blocked_user_record = BlockedUser::where([
            'user_id' => $user->id,
            'blocked_id' => $blocked_user->id
        ])
            ->first();

        // IF BLOCKED USER RECORD NOT EMPTY
        if ($blocked_user_record) {
            throw new GraphQLLogicRestrictException(__('profile.already_blocked'), __('Error'));
        } else {
            // CHECK OF ACTIVE TRANSACTIONS
            if ($blocked_user && BlockHelper::checkCurrentEventsExists($user, $blocked_user)) {
                throw new GraphQLLogicRestrictException(__('privacy.active_events_exists'), __('Error'));
            }

            $blocked_user_record = new BlockedUser();
            $blocked_user_record->user_id = $user->id;
            $blocked_user_record->blocked_id = $blocked_user->id;
            $blocked_user_record->phone_number = $blocked_user->phone;

            if (!$blocked_user_record->save()) {
                throw new GraphQLSaveDataException(__('profile.block_failed'), __('Error'));
            }
        }

        $user->increment('blocked_count');

        BlockHelper::blockChatRooms($user, $blocked_user);

        return $user->blocked_count;
    }
}
