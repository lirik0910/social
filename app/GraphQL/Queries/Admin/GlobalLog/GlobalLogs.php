<?php

namespace App\GraphQL\Queries\Admin\GlobalLog;

use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\GlobalLog\GlobalLogsRequest;
use App\Http\Requests\Admin\GlobalLog\GlobalLogsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\GlobalLog;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GlobalLogs extends AbstractSelection
{
    use DynamicValidation;

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
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, GlobalLogsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('logs', $user);

        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');
        $this->order_by = [
            'column' => 'created_at',
            'dir' => Arr::get($inputs, 'order_by_dir'),
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query instance
     *
     * @return \Jenssegers\Mongodb\Query\Builder |mixed
     */
    public function getBaseQuery()
    {
        $instance = GlobalLog::query();

        if (!empty($this->filter)) {
            if (!empty($this->filter['mutation'])) {
                $instance->where('mutation', $this->filter['mutation']);
            }

            if (!empty($this->filter['section'])) {
                $instance->where('section', $this->filter['section']);
            }

            if (!empty($this->filter['user_nickname'])) {
                $instance->where('user_nickname', 'like', $this->filter['user_nickname'] . '%');
            }

            if (!empty($this->filter['user_id'])) {
                $instance->where('user_id', '=', (int) $this->filter['user_id']);
            }

            if (!empty($this->filter['created_date_period'])) {
                $date_before_start = Carbon::createFromTimeString($this->filter['created_date_period']['from'])->subDay();
                $date_after_end = Carbon::createFromTimeString($this->filter['created_date_period']['to'])->addDay();

                $instance->whereBetween('created_at', [$date_before_start, $date_after_end]);
            } elseif (!empty($this->filter['created_date'])) {
                $date = Carbon::createFromTimeString($this->filter['created_date']);
                $next_day_date = Carbon::createFromTimeString($this->filter['created_date'])->addDay();

                $instance->whereBetween('created_at', [$date, $next_day_date]);
            }
        }

        return $instance;
    }

    /**
     * Return selection`s results total count
     *
     * @param $rootValue
     * @param GlobalLogsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, GlobalLogsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
