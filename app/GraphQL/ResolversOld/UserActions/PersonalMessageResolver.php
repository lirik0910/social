<?php


namespace App\GraphQL\ResolversOld\UserActions;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\PersonalMessage;
use App\Models\User;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class PersonalMessageResolver
{
    use RequestDataValidate;

    /**
     * Create personal message
     *
     * @param $rootValue
     * @param array $args
     * @return PersonalMessage
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        //TODO add validation for user credits count

        //TODO add validation for user personal messages options

        $personal_message = new PersonalMessage();
        $personal_message->user_id = $user->id;
        $personal_message->status = PersonalMessage::STATUS_NEW;

        $personal_message->fill($inputs);

        if(!$personal_message->save()) {
            throw new GraphQLSaveDataException(__('personal_message.create_failed'), __('Error'));
        }

        //TODO operations with user credits

        return $personal_message;
    }

    /**
     * View personal message
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveView($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer'
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $personal_message = PersonalMessage::whereId($id)->firstOrFail();
        $personal_message->status = PersonalMessage::STATUS_VIEWED;

        if(!$personal_message->save()) {
            throw new GraphQLSaveDataException(__('personal_message.update_failed'), __('Error'));
        }

        return $personal_message;
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
            'body' => 'required|string',
        ];
    }
}
