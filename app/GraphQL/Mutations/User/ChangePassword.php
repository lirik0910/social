<?php


namespace App\GraphQL\Mutations\User;


use App\Exceptions\GraphQLSaveDataException;
use App\Traits\RequestDataValidate;
use GraphQL\GraphQL;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangePassword
{
    use RequestDataValidate;

    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * Change User Password
     *
     * @param       $rootValue
     * @param array $args
     * @param GraphQLContext $context
     *
     * @return string
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context)
    {
        $this->user = $context->user();

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $this->user->password = \Hash::make($inputs['password']);

        if ($this->user->save()) {
            return 'success';
        }
        else {
            throw new GraphQLSaveDataException(__('Save data failed'), __('Error'));
        }
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => [
                'required',
                'string',
                'min:8',
                function ($attribute, $value, $fail) {
                    if (!\Hash::check($value, $this->user->password)) {
                        $fail(__('user.old_password_invalid'));
                    }
                }
            ],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

}
