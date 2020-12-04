<?php

namespace App\GraphQL\Queries\Profile;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\BlockHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\User;
use App\Models\Profile as Model;
use App\Traits\DynamicValidation;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Profile
{
    use RequestDataValidate;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $profile_owner_id = $this->validatedData($args, [
                'id' => 'required|string'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = $context->user();
        $user->checkProfileAccessibility($profile_owner_id);

        return Model
            ::where('user_id', $profile_owner_id)
            ->firstOrFail();
    }
}
