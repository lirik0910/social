<?php

namespace App\GraphQL\Queries\Payment;

use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserPaymentTransactions extends AbstractSelection
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
    protected function resolve($rootValue, PaginationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
     * Get favorites selection query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return PaymentTransaction
            ::where(function ($query) {
                $query
                    ->where('to_user_id', $this->user->id)
                    ->where('status', PaymentTransaction::TRANSACTION_STATUS_COMPLETED);
            })
            ->orWhere('from_user_id', $this->user->id)
            ->orWhere('source_type', PaymentTransaction::TRANSACTION_SOURCE_TYPE_PAYMENT_ORDER);
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
