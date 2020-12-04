<?php


namespace App\GraphQL\ResolversOld\PrivateStream;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\PrivateStreamMessage;
use App\Models\PrivateStream;
use App\Traits\RequestDataValidate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PrivateStreamMessageResolver
{
    use RequestDataValidate;

    /**
     * Create private stream message
     *
     * @param $rootValue
     * @param array $args
     * @return PrivateStreamMessage
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
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

        $private_stream = PrivateStream::whereId($inputs['private_stream_id'])->firstOrFail();

        if(!$private_stream->started_at || $private_stream->ended_at) {
            throw new GraphQLLogicRestrictException(__('private_stream.stream_not_active'), __('Error'));
        }

        $private_stream_message = new PrivateStreamMessage();
        $private_stream_message->user_id = $user->id;

        $private_stream_message->fill($inputs);

        if(!$private_stream_message->save()){
            throw new GraphQLSaveDataException(__('private_stream.create_message_failed'), __('Error'));
        }

        return $private_stream_message;
    }

    /**
     * Return rules array
     *
     * @return array
     */
    public function rules()
    {
        return [
            'recipient_id' => 'required|integer|exists:users,id',
            'private_stream_id' => 'required|integer',
            'body' => 'required|string|max:255'
        ];
    }

}
