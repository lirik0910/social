<?php

namespace App\GraphQL\Queries\Admin\Support;

use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Support\AllUsersSupportsRequest;
use App\Http\Requests\Admin\Support\AllUsersSupportsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Report;
use App\Models\Support;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllUsersSupports extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

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
     * @param AllUsersSupportsRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, AllUsersSupportsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();

        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');
        $this->order_by = [
            'column' => 'updated_at',
            'dir' => Arr::get($inputs, 'order_by_dir')
        ];

        AdminPermissionsHelper::check(Support::getPermissionNameByCategory($this->filter['category']), $this->user);

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
        $instance = Support::where('category', $this->filter['category']);

        if (!empty($this->filter['status'])) {
            $instance->where('status', $this->filter['status']);
        }

        if (!empty($this->filter['user'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', 'like', $this->filter['user'] . '%');
            });
        }

        if (!empty($this->filter['moderator'])) {
            $instance->whereHas('moderator', function ($query) {
                $query->where('nickname', 'like', $this->filter['moderator'] . '%');
            });
        }

        if (!empty($this->filter['only_mine'])) {
            $instance->where('moderator_id', $this->user->id);
        }

        if (!empty($this->filter['updated_date_period'])) {
            $instance
                ->whereDate('updated_at', '>=', $this->filter['updated_date_period']['from'])
                ->whereDate('updated_at', '<=', $this->filter['updated_date_period']['to']);
        } elseif (!empty($this->filter['updated_date'])) {
            $instance->whereDate('updated_at', $this->filter['updated_date']);
        }

        return $instance;
    }

    /**
     * Return base query selection`s results total count
     *
     * @param $rootValue
     * @param AllUsersSupportsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllUsersSupportsTotalRequest $args)
    {
        $this->user = Auth::user();

        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
