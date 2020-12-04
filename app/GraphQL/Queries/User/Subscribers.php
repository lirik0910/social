<?php

namespace App\GraphQL\Queries\User;

use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Subscribers extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param PaginationRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
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
     */
    public function getBaseQuery()
    {
        return $this->user
            ->subscribers()
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
