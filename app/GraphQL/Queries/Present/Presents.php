<?php

namespace App\GraphQL\Queries\Present;

use App\Http\Requests\General\IDRequiredRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Present;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;
use App\Http\Requests\Present\PresentsRequest;

class Presents extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Presents category ID
     *
     * @var integer|string
     */
    protected $category_id;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  PresentsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, PresentsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->category_id = Arr::get($inputs, 'category_id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * @return mixed
     */
    public function getBaseQuery()
    {
        return Present
            ::where('category_id', $this->category_id);
    }

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return int
     */
    protected function getTotal($rootValue, IDRequiredRequest $args)
    {
        $this->category_id = Arr::get($args->validated(), 'id');

        return $this->getResultsTotalCount();
    }
}


