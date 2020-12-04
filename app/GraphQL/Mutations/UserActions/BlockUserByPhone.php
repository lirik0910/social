<?php

namespace App\GraphQL\Mutations\UserActions;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\BlockHelper;
use App\Http\Requests\User\Block\BlockUserByPhoneRequest;
use App\Models\BlockedUser;
use App\Models\User;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BlockUserByPhone
{
    use DynamicValidation;

    protected $user;
    protected $blocked_user;

    /**
     * @param $rootValue
     * @param BlockUserByPhoneRequest $args
     * @param GraphQLContext $context
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, BlockUserByPhoneRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $this->blocked_user = User::wherePhone($inputs['phone_number'])->first();

        // EXCLUDE AT AN ATTEMPT TO BLOCK YOURSELF
        if ($this->blocked_user && $this->blocked_user->id == $this->user->id) {
            throw new GraphQLLogicRestrictException(__('profile.you_cant_block_yourself'), __('Error'));
        }

        $blocked_user_record = BlockedUser::where('user_id', $this->user->id)
            ->where(function ($q) use ($inputs) {
                $q->where('phone_number', $inputs['phone_number']);
                if($this->blocked_user){$q->orWhere('blocked_id', $this->blocked_user->id);}
            })
            ->first();

        // IF USER && BLOCKED RECORD
        if (!$blocked_user_record) {
            // CHECK OF ACTIVE TRANSACTIONS
            if ($this->blocked_user && BlockHelper::checkCurrentEventsExists($this->user, $this->blocked_user)) {
                throw new GraphQLLogicRestrictException(__('privacy.active_events_exists'), __('Error'));
            }

            $blocked_user_record = new BlockedUser();
            $blocked_user_record->user_id = $this->user->id;
            $blocked_user_record->phone_number = $inputs['phone_number'];
            $blocked_user_record->blocked_by_phone = true;

            if ($this->blocked_user) {
                $blocked_user_record->blocked_id = $this->blocked_user->id;
            }

            $blocked_user_record->phone_title = $inputs['phone_title'] ?? null;

            if (!$blocked_user_record->save()) {
                throw new GraphQLSaveDataException(__('profile.block_failed'), __('Error'));
            }
        }

        if ($blocked_user_record) {
            if ($this->blocked_user) {
                if (!$blocked_user_record->blocked_id) {
                    $blocked_user_record->blocked_id = $this->blocked_user->id;
                }
                if (!$blocked_user_record->phone_number) {
                    $blocked_user_record->phone_number = $this->blocked_user->phone;
                }
                if (!$blocked_user_record->save()) {
                    throw new GraphQLSaveDataException(__('profile.block_failed'), __('Error'));
                }
            }
        }

        $this->user->increment('blocked_count');

        if (!empty($this->blocked_user)) {
            BlockHelper::blockChatRooms($this->user, $this->blocked_user);
        }

        return $blocked_user_record;
    }
}
