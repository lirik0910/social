<?php


namespace App\GraphQL\ResolversOld\PublicStream;


use App\Events\PublicStream\PublicStreamNewSubscribe;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\PublicStream;
use App\Models\PublicStreamSubscribe;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PublicStreamSubscribeResolver
{
    use RequestDataValidate;

    /**
     * Create subscribe on public stream for user
     *
     * @param $rootValue
     * @param array $args
     * @return array
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $public_stream_id = (int) $this->validatedData($args['data'], [
                'public_stream_id' => 'required|integer'
            ])['public_stream_id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();
        $public_stream = PublicStream::whereId($public_stream_id)->firstOrFail();

        if($public_stream->started_at) {
            throw new GraphQLLogicRestrictException(__('public_stream.is_already_started'), __('Error'));
        }

        $user_age = $user->profile->years;
        if($user_age < $public_stream->min_age || $user_age > $public_stream->max_age) {
            throw new GraphQLLogicRestrictException(__('public_stream.age_restrict'), __('Error'));
        }

        $subscribe_record = PublicStreamSubscribe
            ::where(['user_id' => $user->id, 'public_stream_id' => $public_stream_id])
            ->withTrashed()
            ->first();

        if ($subscribe_record) {
            if(!$subscribe_record->trashed()){
                throw new GraphQLLogicRestrictException(__('public_stream.subscribe_cannot'), __('Error'));
            }

            if(!$subscribe_record->restore()) {
                throw new GraphQLSaveDataException(__('public_stream.subscribe_failed'), __('Error'));
            }
        } else {
            $subscribe_record = new PublicStreamSubscribe();
            $subscribe_record->user_id = $user->id;
            $subscribe_record->public_stream_id = $public_stream_id;

            if(!$subscribe_record->save()) {
                throw new GraphQLSaveDataException(__('public_stream.subscribe_failed'), __('Error'));
            }
        }

        $public_stream->increment('subscribers_count');

        /** Send notification to public stream owner about new subscribe **/
        event(new PublicStreamNewSubscribe($public_stream, $user));

        return [
            'public_stream' => $public_stream,
            'user' => $user
        ];
    }

    /**
     * Delete user subscribe on public stream
     *
     * @param $rootValue
     * @param array $args
     * @return array
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveDelete($rootValue, array $args)
    {
        try {
            $public_stream_id = (int) $this->validatedData($args['data'], [
                'public_stream_id' => 'required|integer'
            ])['public_stream_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();
        $public_stream = PublicStream::whereId($public_stream_id)->firstOrFail();

        if(!PublicStreamSubscribe::where(['user_id' => $user->id, 'public_stream_id' => $public_stream_id])->delete()) {
            throw new GraphQLSaveDataException(__('public_stream.unsubscribe_failed'), __('Error'));
        }

        $public_stream->decrement('subscribers_count');

        return [
            'public_stream' => $public_stream,
            'user' => $user
        ];
    }
}
