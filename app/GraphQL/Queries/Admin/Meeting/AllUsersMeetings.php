<?php

namespace App\GraphQL\Queries\Admin\Meeting;

use App\Http\Requests\Admin\Meeting\AllUsersMeetingsRequest;
use App\Http\Requests\Admin\Meeting\AllUsersMeetingsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Meeting;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllUsersMeetings extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_CREATED_DATE = 'created_at';
    const ORDER_BY_COLUMN_UPDATED_DATE = 'updated_at';
    const ORDER_BY_COLUMN_PRICE = 'price';

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
     * @param  AllUsersMeetingsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllUsersMeetingsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
        $instance = Meeting::query();

        if (!empty($this->filter['status'])) {
            $instance->where('status', $this->filter['status']);
        }

        if (!empty($this->filter['updated_date_period'])) {
            $instance
                ->whereDate('updated_at', '>=', $this->filter['updated_date_period']['from'])
                ->whereDate('updated_at', '<=', $this->filter['updated_date_period']['to']);
        } elseif (!empty($this->filter['updated_date'])) {
            $instance->whereDate('updated_at', $this->filter['updated_date']);
        }

        if (isset($this->filter['free']) && !empty($this->filter['free'])) {
            $instance->where('price', '=', 0);
        } elseif (!empty($this->filter['price_period'])) {
            $instance
                ->where('price', '>=', $this->filter['price_period']['from'])
                ->where('price', '<=', $this->filter['price_period']['to']);
        }

        if (isset($this->filter['charity_only'])) {
            if($this->filter['charity_only']) {
                $instance->whereNotNull('charity_organization_id');
            } else {
                $instance->whereNull('charity_organization_id');
            }
        }

        if (!empty($this->filter['user'])) {
            $instance->where(function ($query) {
                $query
                    ->whereHas('user', function ($query) {
                        $query->where('nickname', 'like', $this->filter['user'] . '%');
                    })
                    ->orWhereHas('seller', function ($query) {
                        $query->where('nickname', 'like', $this->filter['user'] . '%');
                    });
            });
        } else {
            if (!empty($this->filter['buyer'])) {
                $instance->whereHas('user', function ($query) {
                    $query->where('nickname', 'like', $this->filter['buyer'] . '%');
                });
            }

            if (!empty($this->filter['seller'])) {
                $instance->whereHas('seller', function ($query) {
                    $query->where('nickname', 'like', $this->filter['seller'] . '%');
                });
            }
        }

        return $instance;
    }

    /**
     * Return total count for selection`s base query
     *
     * @param $rootValue
     * @param AllUsersMeetingsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllUsersMeetingsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
