<?php


namespace App\GraphQL\ResolversOld\PrivateStream;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\PrivateStream;
use App\Models\UserPrivateStreamsOption;
use App\Events\PrivateStream\PrivateStreamIgnored;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PrivateStreamResolver
{
    use RequestDataValidate;

    /**
     * @var UserPrivateStreamsOption
     */
    protected $seller_private_stream_options;

    /**
     * Create private stream
     *
     * @param $rootValue
     * @param array $args
     * @return PrivateStream
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $seller_id = $this->validatedData($args['data'], [
                'seller_id' => 'required|integer|exists:users,id'
            ])['seller_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();
        $this->seller_private_stream_options = UserPrivateStreamsOption::where('user_id', $seller_id)->firstOrFail();

        //TODO validation for needed count of user credits availability

        $user_age = $user->profile->years;
        if($user_age < $this->seller_private_stream_options->min_age || $user_age > $this->seller_private_stream_options->max_age) {
            throw new GraphQLLogicRestrictException(__('private_stream.age_restrict'), __('Error'));
        }

        $private_stream = new PrivateStream();
        $private_stream->status = PrivateStream::STATUS_NEW;
        $private_stream->user_id = $user->id;
        $private_stream->seller_id = $seller_id;
        $private_stream->tariffing = $this->seller_private_stream_options->tariffing;

        if(!$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.create_failed'), __('Error'));
        }

        return $private_stream;
    }

    /**
     * Accept private stream
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveAccept($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $private_stream = PrivateStream::whereId($id)->firstOrFail();

        if($private_stream->status !== PrivateStream::STATUS_NEW) {
            throw new GraphQLLogicRestrictException(__('private_stream.incorrect_status'), __('Error'));
        }

        $private_stream->status = PrivateStream::STATUS_ACCEPTED;
        $private_stream->started_at = Carbon::now();

        if(!$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.update_failed'), __('Error'));
        }

        return $private_stream;
    }

    /**
     * Reject private stream
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveReject($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $private_stream = PrivateStream::whereId($id)->firstOrFail();

        if($private_stream->status !== PrivateStream::STATUS_NEW) {
            throw new GraphQLLogicRestrictException(__('private_stream.incorrect_status'), __('Error'));
        }

        $private_stream->status = PrivateStream::STATUS_REJECTED;

        if(!$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.update_failed'), __('Error'));
        }

        return $private_stream;
    }

    /**
     * Set private stream status on ignore
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveIgnore($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $private_stream = PrivateStream::whereId($id)->firstOrFail();

        if($private_stream->status !== PrivateStream::STATUS_NEW) {
            throw new GraphQLLogicRestrictException(__('private_stream.incorrect_status'), __('Error'));
        }

        $private_stream->status = PrivateStream::STATUS_IGNORED;

        if(!$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.update_failed'), __('Error'));
        }

        /** Send notification about missed/ignored private stream request **/
        event(new PrivateStreamIgnored($private_stream));

        return $private_stream;
    }

    /**
     * End the private stream
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveEnd($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $private_stream = PrivateStream::whereId($id)->firstOrFail();

        if($private_stream->status !== PrivateStream::STATUS_ACCEPTED) {
            throw new GraphQLLogicRestrictException(__('private_stream.incorrect_status'), __('Error'));
        }

        $private_stream->ended_at = Carbon::now();

        if(!$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.update_failed'), __('Error'));
        }

        //TODO Take credits from user account

        return $private_stream;
    }
}
