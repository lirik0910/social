<?php

namespace App\GraphQL\Queries\Payment;

use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PaymentOrder;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserPaymentOrders extends AbstractSelection
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
     * @return array
     */
    protected function resolve($rootValue, PaginationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo) // TODO: move validation, rewrite pagination
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'column' => 'id',
            'dir' => 'DESC'
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get payment orders selection query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return PaymentOrder
            ::where('user_id', $this->user->id);
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
