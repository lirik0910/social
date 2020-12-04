<?php


namespace App\GraphQL\ResolversOld\UserActions;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\BlockedCountry;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class BlockedCountryResolver
{
    use RequestDataValidate;

    /**
     * Create blocked country
     *
     * @param $rootValue
     * @param array $args
     * @return BlockedCountry
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data'], [
                'country' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        $blocked_country = new BlockedCountry();
        $blocked_country->user_id = $user->id;

        $blocked_country->fill($inputs);

        if(!$blocked_country->save()) {
            throw new GraphQLSaveDataException(__('profile.blocked_country_create_failed'), __('Error'));
        }

        return $blocked_country;
    }

    /**
     * Delete blocked country
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

        $blocked_country = BlockedCountry::whereId($id)->firstOrFail();

        if(!$blocked_country->delete()) {
            throw new GraphQLSaveDataException(__('profile.blocked_country_delete_failed'), __('Error'));
        }

        return [
            'status' => 'Country was unblocked successfully'
        ];
    }
}
