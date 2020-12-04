<?php


namespace App\GraphQL\Resolvers\UserActions;

use App\Events\WantWithYouSent;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\WantWithYou;
use App\Notifications\WantWithYouCreated;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use App\Models\User;

class WantWithYouResolver
{
    use RequestDataValidate;

    /**
     * Create wink
     *
     * @param $rootValue
     * @param array $args
     *
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws \ReflectionException
     */
    public function resolveCreate($rootValue, array $args)
    {
        $who_want = Auth::user();

        try {
            $inputs = $this->validatedData($args['data'], [
                'user_id' => 'required|integer',
                'type' => 'required|integer|in:' . implode(',', array_keys(WantWithYou::availableParams('type')))
            ]);
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user_receiver = User::whereId($inputs['user_id'])->firstOrFail();

        $want_with_you = new WantWithYou();
        $want_with_you->who_want_id = $who_want->id;
        $want_with_you->fill($inputs);

        if(!$want_with_you->save()) {
            throw new GraphQLSaveDataException(__('want_with_you.create_failed'), __('Error'));
        }

        /** Send notification about want with you request **/
        event(new WantWithYouSent($want_with_you->type, $user_receiver));
        //$user_receiver->notify((new WantWithYouCreated($who_want, $inputs['type'])));

        return $want_with_you;
    }
}
