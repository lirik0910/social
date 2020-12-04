<?php

namespace App\GraphQL\Queries\Support;

use App\Http\Requests\Support\SupportsRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Support;
use App\Models\User;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Supports extends AbstractSelection
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
     * @param SupportsRequest $args
     * @param GraphQLContext $context
     * @return mixed
     */
    protected function resolve($rootValue, SupportsRequest $args, GraphQLContext $context)
    {
        $this->user = $context->user();

        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            [
                'dir' => 'DESC',
                'column' => 'updated_at',
            ],
            [
                'dir' => 'ASC',
                'column' => 'status'
            ],
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    public function getBaseQuery()
    {
        return Support
            ::where('user_id', $this->user->id);
    }

    /**
     * Return base query total count
     *
     * @return int
     */
    protected function getTotal()
    {
        $this->user = Auth::user();

        return $this->getResultsTotalCount();
    }
}
