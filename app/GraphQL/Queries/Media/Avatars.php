<?php

namespace App\GraphQL\Queries\Media;

use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Media;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Avatars
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $owner_id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $owner_user = $owner_id == $user->id
            ? $user
            : User
                ::whereId($owner_id)
                ->firstOrFail();

        $query = Media
            ::where('user_id', $owner_user->id)
            ->where('type', Media::TYPE_AVATAR)
            ->orderByDesc('created_at');

        if($user->id !== $owner_user->id) {
            $query->notBanned();
        }

        return $query->get();
    }
}
