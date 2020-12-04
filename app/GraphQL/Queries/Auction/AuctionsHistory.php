<?php


namespace App\GraphQL\Queries\Auction;


use App\Http\Requests\Auction\AuctionHistoryFilterRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use \App\Models\Auction;

class AuctionsHistory extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const TYPE_OWN = 1;
    const TYPE_PARTICIPATE = 2;

    protected $filter;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param AuctionHistoryFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AuctionHistoryFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();
        $this->filter = $args->validated();

        $this->pagination = \Arr::only($this->filter, ['limit', 'offset']);
        $this->order_by = [
            ['column' => 'updated_at', 'dir' => 'DESC'],
            ['column' => 'id', 'dir' => 'ASC'],
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get base query for user meetings selection
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        if ($this->filter['type'] == self::TYPE_OWN) {
            $base_query = Auction::where('user_id', $this->user->id);
        } else {
            $base_query = Auction::where('user_id', '!=', $this->user->id)
                ->whereExists(function ($query) {
                    $query->select(\DB::raw(1))
                        ->from('auction_bids')
                        ->where('auction_bids.user_id', $this->user->id)
                        ->whereRaw('auctions.id = auction_bids.auction_id');
                });
        }

        if ($this->filter['status']) {
            if ($this->filter['status'] === Auction::STATUS_ONGOING) {
                $base_query->active();
            }  else {
                $base_query->where(function ($q) {
                    $q->whereNotNull('cancelled_at')
                        ->orWhere('end_at', '<=', \DB::raw('NOW()'));
                });
            }
        }

        return $base_query;
    }

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param AuctionHistoryFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function getFilterTotal($rootValue, AuctionHistoryFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = \Auth::user();
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
