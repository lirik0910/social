<?php

namespace App\GraphQL\Queries\Admin\CharityOrganization;

use App\Http\Requests\Admin\CharityOrganization\CustomCharityOrganizationsRequest;
use App\Http\Requests\Admin\CharityOrganization\CustomCharityOrganizationsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\CharityOrganization;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CustomCharityOrganizations extends AbstractSelection
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
     * @param  CustomCharityOrganizationsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, CustomCharityOrganizationsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');
        $this->order_by = [
            'column' => 'updated_at',
            'dir' => Arr::get($inputs, 'order_by_dir')
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return selection`s base query
     *
     * @return mixed|void
     */
    public function getBaseQuery()
    {
        $instance = CharityOrganization::whereNotNull('user_id');

        if (!empty($this->filter['nickname'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', 'like', $this->filter['nickname'] . '%');
            });
        }

        if (!empty($this->filter['name'])) {
            $instance->where('name', 'like', $this->filter['name'] . '%');
        }

        if (isset($this->filter['moderation_status'])) {
            $instance->where('moderation_status', $this->filter['moderation_status']);
        }

        return $instance;
    }

    /**
     * Return total count for base query selection
     *
     * @param $rootValue
     * @param CustomCharityOrganizationsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, CustomCharityOrganizationsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
