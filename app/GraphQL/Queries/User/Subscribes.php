<?php

namespace App\GraphQL\Queries\User;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Subscribes extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  PaginationRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, PaginationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'column' => 'pivot_updated_at',
            'dir' => 'DESC'
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get selection base query instance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|mixed
     * @throws GraphQLLogicRestrictException
     */
    public function getBaseQuery()
    {
        return $this->user
            ->subscribes()
            ->withPivot('updated_at');
    }

    /**
     * @return int
     */
    protected function getTotal()
    {
        $this->user = Auth::user();

        return $this->getResultsTotalCount();
    }
}
