<?php


namespace App\GraphQL\ResolversOld\UserActions;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\BlockedPhoneNumber;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class BlockedPhoneNumberResolver
{
    use RequestDataValidate;

    /**
     * Create blocked phone number
     *
     * @param $rootValue
     * @param array $args
     * @return BlockedPhoneNumber
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

        $blocked_phone_number = new BlockedPhoneNumber();
        $blocked_phone_number->user_id = $user->id;

        $blocked_phone_number->fill($inputs);

        if(!$blocked_phone_number->save()) {
            throw new GraphQLSaveDataException(__('profile.blocked_phone_create_failed'), __('Error'));
        }

        return $blocked_phone_number;
    }

    /**
     * Delete blocked phone number
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
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $blocked_phone_number = BlockedPhoneNumber::whereId($id)->firstOrFail();

        if(!$blocked_phone_number->delete()) {
            throw new GraphQLSaveDataException(__('profile.blocked_phone_delete_failed'), __('Error'));
        }

        return [
            'status' => 'Phone number successfully deleted!'
        ];
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' => 'required|max:14',
            'name' => 'nullable|string|max:20',
        ];
    }

}
