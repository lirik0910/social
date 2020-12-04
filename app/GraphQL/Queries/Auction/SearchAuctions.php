<?php

namespace App\GraphQL\Queries\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\Auction\SearchAuctionsRequest;
use App\Http\Requests\Auction\SearchAuctionsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Auction;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\PrivacyTrait;
use App\Traits\ReflectionTrait;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class SearchAuctions extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait, PrivacyTrait;

    const ORDER_BY_PARTICIPANTS = 1;
    const ORDER_BY_TIME = 2;
    const ORDER_BY_RATING = 3;
    const ORDER_BY_LAST_BID = 4;

    /**
     * Searching filter params
     *
     * @var array
     */
    protected $filters;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @var
     */
    protected $border_date;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  SearchAuctionsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, SearchAuctionsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->pagination = Arr::only($inputs, ['limit, offset']);

        $this->order_by = $this->getOrderBy($inputs['order_by']);
        $this->filters = $inputs['filter'];

        $this->border_date = Arr::only($inputs, ['border_date']);

        $results = $this->getResults();

        return [
            'results' => $results,
            'border_date' => $this->border_date
        ];
    }

    /**
     * Get auctions selection query instance
     *
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    public function getBaseQuery()
    {
        $instance = Auction
            ::where(function ($query) {
                $query
                    ->where('users.id', '!=', $this->user->id)
                    ->active($this->border_date);

                if($this->filters) {
                    if(!empty($this->filters['text'])) {
                        $query->where(function ($q) {
                            $q->orWhere('users.nickname', 'like', $this->filters['text'] . '%');
                            $q->orWhere('auctions.description', 'like', $this->filters['text'] . '%');
                        });
                    }

                    if(!empty($this->filters['address'])) {
                        $query->where('auctions.city', 'like', $this->filters['address'] . '%');
                    }

                    $min_age = Carbon::now()->subYears($this->filters['age']['from']);
                    $max_age = Carbon::now()->subYears($this->filters['age']['to']);

                    $query->whereBetween('profiles.age', [$max_age, $min_age]);

                    if(!empty($this->filters['sex'])) {
                        $query->where('profiles.sex', $this->filters['sex']);
                    }

                    $query->whereBetween('auctions.minimal_step', [$this->filters['minimal_step']['from'], $this->filters['minimal_step']['to']]);

                    $query->where(function ($q) {
                        $bids_where_clause = DB::raw("(SELECT max(auction_bids.value) FROM auction_bids WHERE auction_bids.auction_id = auctions.id) between " . $this->filters['latest_bid']['from'] . " and " . $this->filters['latest_bid']['to']);


                        $q->orWhereRaw($bids_where_clause);
                        $q->orWhere(function ($q) {
                            $q->whereDoesntHave('bids');
                            $q->whereBetween('auctions.input_bid', [$this->filters['latest_bid']['from'], $this->filters['latest_bid']['to']]);
                        });
                    });

                    if(!empty($this->filters['charity_only'])) {
                        $query->where('auctions.charity_organization_id', '!=', null);
                    }

                    if(!empty($this->filters['photo_verified_only'])) {
                        $query->where('users.flags', '&', User::FLAG_PHOTO_VERIFIED);
                    }

                    if(!empty($this->filters['end_soon_only'])) {
                        $soonDate = Carbon::now()->addHour();

                        $query->where('auctions.end_at', '<=', $soonDate);
                        $query->where('auctions.end_at', '>', Carbon::now());
                    }
                }
            })
            ->leftJoin('users', 'users.id', '=', 'auctions.user_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'auctions.user_id')
            ->selectRaw('auctions.*')
            ->addSelect(DB::raw("(SELECT max(auction_bids.value) FROM auction_bids WHERE auction_bids.auction_id = auctions.id) as latest_bid"));

        $this->setIgnoredUsers($instance);

        return $instance;
    }

    /**
     * Get selection ordering params
     *
     * @param array $inputs
     * @return mixed
     */
    public function getOrderBy(array $inputs)
    {
        switch ($inputs['column']) {
            case self::ORDER_BY_PARTICIPANTS:
                $order_by['column'] = 'auctions.participants';
                $order_by['dir'] = $inputs['dir'] ?? 'DESC';
                break;
            case self::ORDER_BY_TIME:
                $order_by['column'] = 'auctions.end_at';
                $order_by['dir'] = $inputs['dir'] ?? 'ASC';
                break;
            case self::ORDER_BY_LAST_BID:
                $order_by['column'] = 'latest_bid';
                $order_by['dir'] = $inputs['dir'] ?? 'DESC';
                break;
            case self::ORDER_BY_RATING:
                $order_by['column'] = 'users.meetings_rating';
                $order_by['dir'] = $inputs['dir'] ?? 'DESC';
                break;
            default:
                $order_by['column'] = 'auctions.created_at';

                if(!empty($input_params['dir'])) {
                    $order_by['dir'] = $inputs['dir'] === 'ASC' ? 'DESC' : 'ASC';
                } else {
                    $order_by['dir'] = 'ASC';
                }

                break;
        }

        return $order_by;
    }

    /**
     * @param $rootValue
     * @param SearchAuctionsTotalRequest $args
     * @param GraphQLContext $context
     * @return int
     */
    protected function getTotal($rootValue, SearchAuctionsTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->user = Auth::user();

        $this->filters = $inputs['filter'];
        $this->border_date = $rootValue['border_date'] ?? Carbon::now();

        return $this->getResultsTotalCount();
    }
}
