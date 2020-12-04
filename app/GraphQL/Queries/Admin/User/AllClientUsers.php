<?php

namespace App\GraphQL\Queries\Admin\User;

use App\Http\Requests\Admin\User\AllClientUsersRequest;
use App\Http\Requests\Admin\User\AllClientUsersTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllClientUsers extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_AGE = 'profiles.age';
    const ORDER_BY_COLUMN_RATING = 'users.meetings.rating';
    const ORDER_BY_COLUMN_CREATED_DATE = 'users.created_at';
    const ORDER_BY_COLUMN_BALANCE = 'users.balance';
    const ORDER_BY_COLUMN_REPORTS_COUNT = 'users.reports_count';

    /**
     * Selection`s filter
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param AllClientUsersRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllClientUsersRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = User
            ::where('users.role', User::ROLE_USER)
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->select(['users.*', 'profiles.age', 'profiles.country', 'profiles.sex']);

        if (!empty($this->filter['sex'])) {
            $instance->where('profiles.sex', $this->filter['sex']);
        }

        if (!empty($this->filter['country'])) {
            $instance->where('profiles.country', 'like', $this->filter['country']);
        }

        if (!empty($this->filter['age'])) {
            $date_to = Carbon::now()->subYears($this->filter['age']['from']);
            $date_from = Carbon::now()->subYears($this->filter['age']['to']);

            $instance
                ->whereDate('profiles.age', '>=', $date_from)
                ->whereDate('profiles.age', '<=', $date_to);
        }

        if (!empty($this->filter['nickname'])) {
            $instance->where('users.nickname', 'like', $this->filter['nickname'] . '%');
        }

        if (isset($this->filter['banned'])) {
            if (!empty($this->filter['banned'])) {
                $instance->whereNotNull('users.ban_id');
            } else {
                $instance->whereNull('users.ban_id');
            }
        }

        if (!empty($this->filter['created_date_period'])) {
            $instance
                ->whereDate('users.created_at', '>=', $this->filter['created_date_period']['from'])
                ->whereDate('users.created_at', '<=', $this->filter['created_date_period']['to']);
        } elseif (!empty($this->filter['created_date'])) {
            $instance->whereDate('users.created_at', $this->filter['created_date']);
        }

        return $instance;
    }

    /**
     * Return selection`s base query results total count
     *
     * @param $rootValue
     * @param AllClientUsersTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllClientUsersTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
