<?php

namespace App\GraphQL\Queries\CharityOrganization;

use App\Http\Requests\Charity\CharityFilterRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\CharityOrganization;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Charities extends AbstractSelection
{
    use DynamicValidation;

    protected $user;
    protected $search;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param CharityFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, CharityFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            ['column' => 'user_id', 'dir' => 'DESC'],
            ['column' => 'id', 'dir' => 'ASC'],
        ];

        if (array_key_exists('search', $inputs))
            $this->search = $inputs['search'];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param CharityFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return int
     */
    protected function getFilterTotal($rootValue, CharityFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = \Auth::user();
        $inputs = $args->validated();

        if ($inputs['search']) {
            $this->search = $inputs['search'];
            $this->query_instance = clone ($this->getBaseQuery());
            $this->setAdditionalClauses();

            return $this->query_instance->count();
        } else {
            return $this->getResultsTotalCount();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseQuery()
    {
        return CharityOrganization::available();
    }

    protected function setAdditionalClauses()
    {
        if ($this->search) {
            $this->query_instance->where('name', 'like', "%{$this->search}%");
        }
    }
}
