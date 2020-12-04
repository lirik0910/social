<?php

namespace App\GraphQL\Queries\Admin\PaymentTransaction;

use App\Http\Requests\Admin\PaymentTransaction\AllPaymentTransactionsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PaymentTransaction;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Http\Requests\Admin\PaymentTransaction\AllPaymentTransactionsRequest;

class AllPaymentTransactions extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_UPDATED_DATE = 'updated_at';
    const ORDER_BY_COLUMN_AMOUNT = 'amount';

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
     * @param  AllPaymentTransactionsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllPaymentTransactionsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = Arr::get($inputs, 'order_by');
        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query instance
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function getBaseQuery()
    {
        $instance = PaymentTransaction::query();

        if (!empty($this->filter)) {
            if (!empty($this->filter['from_user'])) {
                $instance->whereHas('from_user', function ($query) {
                    $query->where('nickname', 'like', $this->filter['from_user'] . '%');
                });
            }

            if (!empty($this->filter['to_user'])) {
                $instance->whereHas('to_user', function ($query) {
                    $query->where('nickname', 'like', $this->filter['to_user'] . '%');
                });
            }

            if (!empty($this->filter['source_type'])) {
                $instance->where('source_type', $this->filter['source_type']);
            }

            if (!empty($this->filter['status'])) {
                $instance->where('status', $this->filter['status']);
            }

            if (!empty($this->filter['amount_period'])) {
                $instance->where('amount', '>=', $this->filter['amount_period']['from']);
                $instance->where('amount', '<=', $this->filter['amount_period']['to']);
            }

            if (!empty($this->filter['updated_date_period'])) {
                $instance
                    ->whereDate('updated_at', '>=', $this->filter['updated_date_period']['from'])
                    ->whereDate('updated_at', '<=', $this->filter['updated_date_period']['to']);
            } elseif (!empty($this->filter['updated_date'])) {
                $instance->whereDate('updated_at', $this->filter['updated_date']);
            }
        }

        return $instance;
    }

    /**
     * Return selection`s base query results total count
     *
     * @param $rootValue
     * @param AllPaymentTransactionsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllPaymentTransactionsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
