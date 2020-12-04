<?php

namespace App\GraphQL\Queries\User;

use App\Http\Requests\User\SearchUsersTotalRequest;
use App\Http\Requests\User\SearchUsersRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\PrivacyTrait;
use App\Traits\ReflectionTrait;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;


class SearchUsers extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait, PrivacyTrait;

    const ORDER_BY_CREATED = 1;
    const ORDER_BY_AGE = 2;
    const ORDER_BY_PRICE = 3;
    const ORDER_BY_RATING = 4;

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
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param SearchUsersRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, SearchUsersRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = $this->getOrderBy($inputs['order_by']);
        $this->filters = $inputs['filter'];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * @return mixed
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public function getBaseQuery()
    {
        $instance = User
            ::where(function ($query) {
                $query
                    ->where('users.id', '!=', $this->user->id)
                    ->whereRaw('(users.flags & ' . User::FLAG_REQUIRED_FILL_PROFILE . ') = 0');

                if ($this->filters) {
                    if (!empty($this->filters['nickname'])) {
                        $query->where('nickname', 'like', $this->filters['nickname'] . '%');
                    }

                    if (!empty($this->filters['address'])) {
                        $query->where('address', 'like', $this->filters['address'] . '%');
                    }

                    $min_age = Carbon::now()->subYears($this->filters['age']['from']);
                    $max_age = Carbon::now()->subYears($this->filters['age']['to']);

                    $query->whereBetween('profiles.age', [$max_age, $min_age]);

                    if (!empty($this->filters['height'])) {
                        $query->whereBetween('profiles.height', [$this->filters['height']['from'], $this->filters['height']['to']]);
                    }

                    if (!empty($this->filters['sex'])) {
                        $query->where('profiles.sex', $this->filters['sex']);
                    }

                    if (!empty($this->filters['physique'])) {
                        $query->where('profiles.physique', $this->filters['physique']);
                    }

                    if (!empty($this->filters['eye'])) {
                        $query->where('profiles.eye_color', $this->filters['eye']);
                    }

                    if (!empty($this->filters['hair'])) {
                        $query->where('profiles.hair_color', $this->filters['hair']);
                    }

                    if (!empty($this->filters['free_only'])) {
                        $query->where('user_meetings_options.minimal_price', 0);
                    } else {
                        $query->whereBetween('user_meetings_options.minimal_price', [$this->filters['meeting_cost']['from'], $this->filters['meeting_cost']['to']]);
                    }

                    if (!empty($this->filters['charity_only'])) {
                        $query->where('user_meetings_options.charity_organization_id', '!=', null);
                    }

                    if (!empty($this->filters['new_only'])) {
                        $query->where('users.created_at', '>', Carbon::now()->subWeek());
                    }

                    if (!empty($this->filters['safe_deal_only'])) {
                        $query->where('user_meetings_options.safe_deal_only', true);
                    }

                    if (!empty($this->filters['photo_verified_only'])) {
                        $query->where('flags', '&', User::FLAG_PHOTO_VERIFIED);
                    }
                }
            })
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->leftJoin('user_meetings_options', 'users.id', '=', 'user_meetings_options.user_id')
            ->select([
                'users.*',
                'user_meetings_options.minimal_price as meeting_price',
                'user_meetings_options.charity_organization_id as charity_organization_id',
                'user_meetings_options.safe_deal_only',
            ]);

        $this->setIgnoredUsers($instance);

        return $instance;
    }

    /**
     * Get order by params
     *
     * @param array $inputs
     * @return mixed
     */
    public function getOrderBy(array $inputs)
    {
        switch ($inputs['column']) {
            case self::ORDER_BY_AGE:
                $order_by['column'] = 'profiles.age';

                if (!empty($inputs['dir'])) {
                    $order_by['dir'] = $inputs['dir'] === 'ASC' ? 'DESC' : 'ASC';
                } else {
                    $order_by['dir'] = 'ASC';
                }
                break;
            case self::ORDER_BY_PRICE:
                $order_by['column'] = 'user_meetings_options.minimal_price';
                $order_by['dir'] = $inputs['dir'] ?? 'ASC';
                break;
            case self::ORDER_BY_RATING:
                $order_by['column'] = 'users.meetings_rating';
                $order_by['dir'] = $inputs['dir'] ?? 'DESC';
                break;
            default:
                $order_by['column'] = 'users.created_at';

                if (!empty($inputs['dir'])) {
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
     * @param SearchUsersTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, SearchUsersTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->user = Auth::user();
        $this->filters = $inputs['filter'];

        return $this->getResultsTotalCount();
    }
}
