<?php


namespace App\GraphQL\ResolversOld\PrivateStream;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Present;
use App\Models\PrivateStream;
use App\Models\PrivateStreamPresent;
use Illuminate\Support\Facades\Auth;
use App\Traits\RequestDataValidate;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PrivateStreamPresentResolver
{
    use RequestDataValidate;

    /**
     * Create present for private stream
     *
     * @param $rootValue
     * @param array $args
     * @return array
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        $user = Auth::user();

        //TODO add validation for user credit balance

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $private_stream = PrivateStream::whereId($inputs['private_stream_id'])->firstOrFail();

        if(!$private_stream->started_at || $private_stream->ended_at) {
            throw new GraphQLLogicRestrictException(__('private_stream.stream_not_active'), __('Error'));
        }

        $present = Present::whereId($inputs['present_id'])->firstOrFail();

        $private_stream_present = new PrivateStreamPresent();
        $private_stream_present->user_id = $user->id;
        $private_stream_present->price = $present->price;

        $private_stream_present->fill($inputs);

        $private_stream->presents_cost += $present->price;

        if (!$private_stream_present->save() || !$private_stream->save()) {
            throw new GraphQLSaveDataException(__('private_stream.create_present_failed'), __('Error'));
        }

        return [
            'present' => $private_stream_present,
            'private_stream' => $private_stream
        ];
    }

    /**
     * Return rules array
     *
     * @return array
     */
    public function rules()
    {
        return [
            'private_stream_id' => 'required|integer',
            'present_id' => 'required|integer',
        ];
    }
}
