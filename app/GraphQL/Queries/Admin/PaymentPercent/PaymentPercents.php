<?php

namespace App\GraphQL\Queries\Admin\PaymentPercent;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;

use App\Http\Requests\Admin\PaymentPercent\AllPaymentPercentsRequest;
use App\Http\Requests\Admin\PaymentPercent\AllPaymentPercentsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\PaymentPercent;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use Illuminate\Support\Arr;

class PaymentPercents extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_CREATED_DATE = 'created_at';
    const ORDER_BY_COLUMN_PERCENT = 'percent';

    /**
     * Selection`s filter
     *
     * @var array
     */
    protected $filter;

    /**
     * @param $rootValue
     * @param AllPaymentPercentsRequest $args
     * @return array
     */
    protected function resolve($rootValue, AllPaymentPercentsRequest $args)
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
        $instance = PaymentPercent::query();

        if (!empty($this->filter)) {
            if (!empty($this->filter['model'])) {
                $instance->where('model', $this->filter['model']);
            }

            if (!empty($this->filter['type'])) {
                $instance->where('type', $this->filter['type']);
            }

            if (isset($this->filter['status'])) {
                $instance->where('status', $this->filter['status']);
            }
        }

        return $instance;
    }

    /**
     * Return selection`s base query instance results total count
     *
     * @param $rootValue
     * @param AllPaymentPercentsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllPaymentPercentsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
