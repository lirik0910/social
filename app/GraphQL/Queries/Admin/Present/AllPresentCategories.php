<?php

namespace App\GraphQL\Queries\Admin\Present;

use App\Http\Requests\Admin\Present\AllPresentCategoriesRequest;
use App\Http\Requests\Admin\Present\AllPresentCategoriesTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PresentCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllPresentCategories extends AbstractSelection
{
    use DynamicValidation;

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
     * @param  AllPresentCategoriesRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllPresentCategoriesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return instance for base query
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function getBaseQuery()
    {
        $instance = PresentCategory::query();

        if(isset($this->filter['availability'])) {
            $instance->where('available', $this->filter['availability']);
        }

        return $instance;
    }

    /**
     * Return total count for base query
     *
     * @param $rootValue
     * @param AllPresentCategoriesTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllPresentCategoriesTotalRequest $args)
    {
        $this->filter = Arr::get($args->validated(), 'availability');

        return $this->getResultsTotalCount();
    }
}
