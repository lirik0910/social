<?php

namespace App\GraphQL\Queries\Admin\CharityOrganization;

use App\Http\Requests\Admin\CharityOrganization\NativeCharityOrganizationsRequest;
use App\Http\Requests\Admin\CharityOrganization\NativeCharityOrganizationsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\CharityOrganization;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class NativeCharityOrganizations extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_DATE = 'created_at';
    const ORDER_BY_COLUMN_BALANCE = 'balance';

    /**
     * Selection`s filter
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  NativeCharityOrganizationsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, NativeCharityOrganizationsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');
        $this->order_by = Arr::get($inputs, 'order_by');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = CharityOrganization::whereNull('user_id');

        if (isset($this->filter['available'])) {
            $instance->where('available', $this->filter['available']);
        }

        if (!empty($this->filter['name'])) {
            $instance->where('name', 'like', $this->filter['name'] . '%');
        }

        return $instance;
    }

    /**
     * Return total count for base query selection
     *
     * @param $rootValue
     * @param NativeCharityOrganizationsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, NativeCharityOrganizationsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
