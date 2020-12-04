<?php


namespace App\GraphQL\ResolversOld\Meeting;

use App\Exceptions\GraphQLSaveDataException;
use App\Models\WantWithYou;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

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
                'user_id' => 'required|integer|exists:users,id',
                'type' => 'required|integer|in:' . implode(',', array_keys(WantWithYou::availableParams('type')))
            ]);
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $want_with_you = new WantWithYou();
        $want_with_you->who_want_id = $who_want->id;
        $want_with_you->fill($inputs);

        if(!$want_with_you->save()) {
            throw new GraphQLSaveDataException(__('want_with_you.create_failed'), __('Error'));
        }

        return $want_with_you;
    }
}
