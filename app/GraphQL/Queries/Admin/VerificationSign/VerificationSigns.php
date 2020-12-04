<?php

namespace App\GraphQL\Queries\Admin\VerificationSign;

use App\Http\Requests\Admin\Common\AvailabilityTotalRequest;
use App\Http\Requests\Admin\VerificationSign\VerificationSignsRequest;
use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PhotoVerification;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class VerificationSigns extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    /**
     * Selection`s filters
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  VerificationSignsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, VerificationSignsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'column' => 'created_at',
            'dir' => Arr::get($inputs, 'order_by_dir')
        ];
        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return base query
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function getBaseQuery()
    {
        $instance = PhotoVerification::query();

        if (isset($this->filter['available'])) {
            $instance->where('available', $this->filter['available']);
        }

        return $instance;
    }

    /**
     * Return total count for base query
     *
     * @param $rootValue
     * @param AvailabilityTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AvailabilityTotalRequest $args)
    {
        $this->filter['available'] = Arr::get($args->validated(), 'available');

        return $this->getResultsTotalCount();
    }
}
