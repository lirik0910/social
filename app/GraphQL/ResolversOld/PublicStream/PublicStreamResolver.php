<?php


namespace App\GraphQL\ResolversOld\PublicStream;


use App\Models\PublicStream;
use App\Events\PublicStream\PublicStreamStarted;
use App\Events\PublicStream\PublicStreamPlanned;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use App\Exceptions\GraphQLSaveDataException;
use App\Exceptions\GraphQLLogicRestrictException;

class PublicStreamResolver
{
    use RequestDataValidate;

    protected $user;

    /**
     * Create public stream record
     *
     * @param       $rootValue
     * @param array $args
     * @return PublicStream
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        if(empty($inputs['preview']) && !$user->image) {
            throw new GraphQLLogicRestrictException(__('public_stream.preview_not_exist'), __('Error'));
        }

        $public_stream = new PublicStream();
        $public_stream->user_id = $user->id;

        if(empty($inputs['planned_at'])) {
            $public_stream->started_at = Carbon::now();
        }

        $public_stream->fill($inputs);

        if (!$public_stream->save()) {
            throw new GraphQLSaveDataException(__('public_stream.create_failed'), __('Error'));
        }

        /** Send notifications about started or planned stream **/
        if($public_stream->started_at) {
            event(new PublicStreamStarted($public_stream));
        } elseif(!empty($public_stream->planned_at)) {
            event(new PublicStreamPlanned($public_stream, $user));
        }

        return $public_stream;
    }

    /**
     * Update public stream record
     *
     * @param       $rootValue
     * @param array $args
     * @return PublicStream
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveUpdate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
             throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        $public_stream = PublicStream::where('id', $inputs['id'])->firstOrFail();

        if(!empty($public_stream->started_at)){
            throw new GraphQLLogicRestrictException(__('public_stream.cannot_update'), __('Error'));
        }

        if(empty($inputs['planned_at'])) {
           $public_stream->started_at = Carbon::now();
        } else {
            $previous_planned_at = $public_stream->planned_at;
        }

        $public_stream->fill($inputs);

        if (!$public_stream->save()) {
            throw new GraphQLSaveDataException(__('public_stream.update_failed'), __('Error'));
        }

        /** Send notifications about start or planned public stream (if planned date changes) **/
        if($public_stream->started_at) {
            event(new PublicStreamStarted($public_stream));
        } elseif(!empty($previous_planned_at) && $previous_planned_at !== $public_stream->planned_at) {
            // TODO delete previous jobs from database because they have incorrect release time (planned stream time was changed so release time have changed too)
            event(new PublicStreamPlanned($public_stream, $user));
        }

        return $public_stream;
    }

    /**
     * Start public stream
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveStart($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $public_stream = PublicStream::whereId($id)->firstOrFail();

        if($public_stream->started_at) {
            throw new GraphQLLogicRestrictException(__('public_stream.already_started'), __('Error'));
        }

        $public_stream->started_at = Carbon::now();

        if(!$public_stream->save()) {
            throw new GraphQLSaveDataException(__('public_stream.update_failed'), __('Error'));
        }

        /** Send notifications about start public stream **/
        event(new PublicStreamStarted($public_stream));

        return $public_stream;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    protected function rules()
    {
       $rules = [
           'id' => 'sometimes|required|integer',
           'preview' => 'nullable|string',
           'title' => 'required|string|max:32',
           'description' => 'required|string|max:74',
           'tariffing' => 'required|integer|max:4294967200',
           'message_cost' => 'required|integer|max:4294967200',
           'min_age' => 'required|integer|min:0|max:99|lte:max_age',
           'max_age' => 'required|integer|min:0|max:99|gte:min_age',
           'for_subscribers_only' => 'required|boolean',
           'planned_at' => 'nullable|date'
       ];

       return $rules;
    }
}
