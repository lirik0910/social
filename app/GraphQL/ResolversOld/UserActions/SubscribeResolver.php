<?php


namespace App\GraphQL\ResolversOld\UserActions;


use App\Events\NewSubscribe;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\Subscribe;
use App\Models\User;
use App\Exceptions\GraphQLSaveDataException;
use App\Notifications\SubscribeCreated;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class SubscribeResolver
{
    use RequestDataValidate;

    /**
     * Create subscribe from user to another user
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $subscribe_user_id = (int) $this->validatedData($args, [
                'subscribe_user_id' => 'required|integer'
            ])['subscribe_user_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();
        $subscribe_user = User::whereId($subscribe_user_id)->firstOrFail();

        //Current subscribe record
        $subscribe_record = Subscribe
            ::where(['user_id' => $subscribe_user_id, 'subscriber_id' => $user->id])
            ->withTrashed()
            ->first();

        if ($subscribe_record) {
            if(!$subscribe_record->trashed()){
                throw new GraphQLLogicRestrictException(__('profile.subscribe_cannot'), __('Error'));
            }

            if(!$subscribe_record->restore()) {
                throw new GraphQLSaveDataException(__('profile.subscribe_failed'), __('Error'));
            }
        } else {
            $subscribe_record = new Subscribe();
            $subscribe_record->user_id = $subscribe_user_id;
            $subscribe_record->subscriber_id = $user->id;

            if(!$subscribe_record->save()) {
                throw new GraphQLSaveDataException(__('profile.subscribe_failed'), __('Error'));
            }
        }

        $user->increment('subscribes_count');
        $subscribe_user->increment('subscribers_count');

        /** Send notification about new subscribed user  **/
        event(new NewSubscribe($user, $subscribe_user));

        return [
            'subscribe_user' => $subscribe_user,
            'subscriber' => $user
        ];
    }

    /**
     * Delete subscribe from user to another user
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
            $subscribe_user_id = (int) $this->validatedData($args, [
                'subscribe_user_id' => 'required|integer'
            ])['subscribe_user_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();
        $subscribe_user = User::whereId($subscribe_user_id)->firstOrFail();

        if(!Subscribe::where(['user_id' => $subscribe_user_id, 'subscriber_id' => $user->id])->delete()) {
            throw new GraphQLSaveDataException(__('profile.unsubscribe_failed'), __('Error'));
        }

        $user->decrement('subscribes_count');
        $subscribe_user->decrement('subscribers_count');

        return [
            'subscribe_user' => $subscribe_user,
            'subscriber' => $user
        ];
    }
}
