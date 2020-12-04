<?php

namespace App\GraphQL\Queries\Admin\User;

use App\Http\Requests\Admin\User\AllAdminUsersRequest;
use App\Http\Requests\Admin\User\AllAdminUsersTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllAdminUsers extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const ORDER_BY_COLUMN_CREATED_DATE = 'users.created_at';

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
     * @param  AllAdminUsersRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllAdminUsersRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
        $instance = User::where('role', '!=', User::ROLE_USER);

        if (!empty($this->filter['nickname'])) {
            $instance->where('users.nickname', 'like', $this->filter['nickname'] . '%');
        }

        if (!empty($this->filter['role'])) {
            $instance->where('users.role', '=', $this->filter['role']);
        }

        if (!empty($this->filter['permissions'])) {
            foreach ($this->filter['permissions'] as $permission) {
                $instance
                    ->whereRaw('(permissions & ' . $permission . ') != 0')
                    ->orWhere('users.role', '=', User::ROLE_ROOT);
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
     * @param AllAdminUsersTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllAdminUsersTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
