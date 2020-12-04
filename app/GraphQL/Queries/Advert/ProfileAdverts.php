<?php

namespace App\GraphQL\Queries\Advert;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\BlockHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Advert;
use App\Traits\DynamicValidation;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;

class ProfileAdverts
{
    use DynamicValidation;

    /**
     * Page owner`s ID
     *
     * @var integer|string
     */
    protected $page_owner_id;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->page_owner_id = Arr::get($inputs, 'id');

        $user = $context->user();
        $user->checkProfileAccessibility($this->page_owner_id);

        return $this->getAdverts();
    }

    /**
     * Return page owner`s adverts
     *
     * @return mixed
     */
    protected function getAdverts()
    {
        return Advert
            ::where('user_id', $this->page_owner_id)
            ->active()
            ->get();
    }
}
