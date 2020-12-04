<?php

namespace App\GraphQL\Queries\Admin\Auction;

use App\Http\Requests\Admin\Auction\AllUsersAuctionsRequest;
use App\Http\Requests\Admin\Auction\AllUsersAuctionsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Auction;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllUsersAuctions extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_CREATED_DATE = 'auctions.created_at';
    const ORDER_BY_COLUMN_END_AT_DATE = 'auctions.end_at';
    const ORDER_BY_COLUMN_LATEST_BID = 'auction_bids.value';
    const ORDER_BY_COLUMN_PARTICIPANTS = 'auctions.participants';

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
     * @param  AllUsersAuctionsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllUsersAuctionsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
     * @return mixed|void
     */
    public function getBaseQuery()
    {
        $instance = Auction::query();

        if (isset($this->filter['verified_only'])) {
            $instance->where('auctions.photo_verified_only', $this->filter['verified_only']);
        }

        if (isset($this->filter['charity_only'])) {
            if($this->filter['charity_only'] == true) {
                $instance->whereNotNull('auctions.charity_organization_id');
            } else {
                $instance->whereNull('auctions.charity_organization_id');
            }
        }

        if (!empty($this->filter['created_date_period'])) {
            $instance
                ->whereDate('auctions.created_at', '>=', $this->filter['created_date_period']['from'])
                ->whereDate('auctions.created_at', '<=', $this->filter['created_date_period']['to']);
        } elseif (!empty($this->filter['created_date'])) {
            $instance->whereDate('auctions.created_at', $this->filter['created_date']);
        }

        if (!empty($this->filter['user'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', 'like', $this->filter['user'] . '%');
            });
        }

        $instance
            ->leftJoin('auction_bids', 'auction_bids.id', '=', 'auctions.last_bid_id')
            ->select(['auctions.*', 'auction_bids.value as latest_bid_value']);

        return $instance;
    }

    /**
     * Return selection`s base query results count
     *
     * @param $rootValue
     * @param AllUsersAuctionsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllUsersAuctionsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }

}
