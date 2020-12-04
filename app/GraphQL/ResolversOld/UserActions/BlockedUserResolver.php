<?php


namespace App\GraphQL\ResolversOld\UserActions;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\BlockedUser;
use App\Models\User;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class BlockedUserResolver
{
    use RequestDataValidate;

    /**
     * Create blocked user record
     *
     * @param $rootValue
     * @param array $args
     * @return array
     *
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $blocked_user_id = (int) $this->validatedData($args, [
                'blocked_user_id' => 'required|integer|exists:users,id'
            ])['blocked_user_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        //Current block user record
        $blocked_user_record = BlockedUser
            ::where(['user_id' => $user->id, 'blocked_id' => $blocked_user_id])
            ->withTrashed()
            ->first();

        if ($blocked_user_record) {
            if(!$blocked_user_record->trashed()){
                throw new GraphQLLogicRestrictException(__('profile.block_cannot'), __('Error'));
            }

            if(!$blocked_user_record->restore()) {
                throw new GraphQLSaveDataException(__('profile.block_failed'), __('Error'));
            }
        } else {
            $blocked_user_record = new BlockedUser();
            $blocked_user_record->user_id = $user->id;
            $blocked_user_record->blocked_id = $blocked_user_id;

            if(!$blocked_user_record->save()) {
                throw new GraphQLSaveDataException(__('profile.block_failed'), __('Error'));
            }
        }

        $user->increment('blocked_count');

        return [
            'user' => $user
        ];
    }

    /**
     * Unblock user
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveDelete($rootValue, array $args)
    {
        try {
            $blocked_user_id = (int) $this->validatedData($args, [
                'blocked_user_id' => 'required|integer|exists:users,id'
            ])['blocked_user_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        if(!BlockedUser::where(['user_id' => $user->id, 'blocked_id' => $blocked_user_id])->delete()) {
            throw new GraphQLSaveDataException(__('profile.unblock_failed'), __('Error'));
        }

        $user->decrement('blocked_count');

        return [
            'user' => $user
        ];
    }
}
