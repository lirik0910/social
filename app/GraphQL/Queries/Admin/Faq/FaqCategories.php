<?php

namespace App\GraphQL\Queries\Admin\Faq;

use App\Http\Requests\Admin\Faq\FaqCategoriesRequest;
use App\Http\Requests\Admin\Faq\FaqCategoriesTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\FaqCategory;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FaqCategories extends AbstractSelection
{
    use DynamicValidation;

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
     * @param  FaqCategoriesRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, FaqCategoriesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = FaqCategory::where('locale', $this->filter['locale']);

        if (isset($this->filter['status'])) {
            $instance->where('status', $this->filter['status']);
        }

        return $instance;
    }

    /**
     * Return base query selection`s results total count
     *
     * @param $rootValue
     * @param FaqCategoriesTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, FaqCategoriesTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
