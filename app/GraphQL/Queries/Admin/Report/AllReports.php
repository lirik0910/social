<?php

namespace App\GraphQL\Queries\Admin\Report;

use App\Http\Requests\Admin\Report\AllReportsRequest;
use App\Http\Requests\Admin\Report\AllReportsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Report;
use App\Traits\DynamicValidation;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllReports extends AbstractSelection
{
    use DynamicValidation, RequestDataValidate;

    /**
     * Selection`s filter
     *
     * @var array
     */
    protected $filter;

    /**
     * Report`s type
     *
     * @var string
     */
    protected $type;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  AllReportsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, AllReportsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->type = Arr::get($inputs, 'type');
        $this->filter = Arr::get($inputs, 'filter');

        if (!empty($this->filter['reason'])) {
            try {
                $this->filter['reason'] = $this->validatedData($this->filter, [
                    'reason' => 'in:' . implode(',', array_keys(Report::availableParams('reason_' . $this->type)))
                ]) ['reason'];
            }  catch (ValidationException $e) {
                throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
            }
        }

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'dir' => Arr::get($inputs, 'order_by_dir'),
            'column' => 'reports.created_at',
        ];

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
        $instance = Report::where('reported_type', $this->type);

        if (isset($this->filter['status'])) {
            $instance->where('reports.status', $this->filter['status']);
        }

        if (!empty($this->filter['reported_user'])) {
            $instance->leftJoin('users', 'reports.reported_user_id', '=', 'users.id');
            $instance->where('users.nickname', 'like', $this->filter['reported_user'] . '%');
        }

        if (!empty($this->filter['reason'])) {
            $instance->where('reports.reason', $this->filter['reason']);
        }

        $instance->select(['reports.*']);

        return $instance;
    }

    /**
     * @param $rootValue
     * @param AllReportsTotalRequest $args
     * @return int
     * @throws GraphQLValidationException
     * @throws \ReflectionException
     */
    protected function getTotal($rootValue, AllReportsTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->type = Arr::get($inputs, 'type');
        $this->filter = Arr::get($inputs, 'filter');

        if (!empty($this->filter['reason'])) {
            try {
                $this->filter['reason'] = $this->validatedData($this->filter, [
                    'reason' => 'in:' . implode(',', array_keys(Report::availableParams('reason_' . $this->type)))
                ]) ['reason'];
            }  catch (ValidationException $e) {
                throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
            }
        }
        return $this->getResultsTotalCount();
    }
}
