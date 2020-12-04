<?php

namespace App\GraphQL\Queries\Admin\Advert;

use App\Http\Requests\Admin\Advert\AllUsersAdvertsRequest;
use App\Http\Requests\Admin\Advert\AllUsersAdvertsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Advert;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllUsersAdverts extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_CREATED_DATE = 'created_at';
    const ORDER_BY_COLUMN_PRICE = 'price';
    const ORDER_BY_COLUMN_PARTICIPANTS = 'participants';

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
     * @param  AllUsersAdvertsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllUsersAdvertsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');
        $this->order_by = Arr::get($inputs, 'order_by');

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
       $instance = Advert::query();

        if (!empty($this->filter['type'])) {
            $instance->where('type', $this->filter['type']);
        }

        if (!empty($this->filter['created_date_period'])) {
            $instance
                ->whereDate('created_at', '>=', $this->filter['created_date_period']['from'])
                ->whereDate('created_at', '<=', $this->filter['created_date_period']['to']);
        } elseif (!empty($this->filter['created_date'])) {
            $instance->whereDate('created_at', $this->filter['created_date']);
        }

        if (isset($this->filter['charity_only'])) {
            if($this->filter['charity_only']) {
                $instance->whereNotNull('charity_organization_id');
            } else {
                $instance->whereNull('charity_organization_id');
            }
        }

        if (isset($this->filter['free']) && !empty($this->filter['free'])) {
            $instance->where('price', '=', 0);
        } elseif (!empty($this->filter['price_period'])) {
            $instance
                ->where('price', '>=', $this->filter['price_period']['from'])
                ->where('price', '<=', $this->filter['price_period']['to']);
        }

        if (!empty($this->filter['user'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', $this->filter['user']);
            });
        }

        return $instance;
    }

    /**
     * Return selection`s base query total count
     *
     * @param $rootValue
     * @param AllUsersAdvertsTotalRequest $args
     * @return integer
     */
    protected function getTotal($rootValue, AllUsersAdvertsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
