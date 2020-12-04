<?php

namespace App\GraphQL\Queries\Admin\ProfilesBackground;

use App\Http\Requests\Admin\ProfilesBackground\ProfilesBackgroundsRequest;
use App\Http\Requests\Admin\ProfilesBackground\ProfilesBackgroundsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\ProfilesBackground;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ProfilesBackgrounds extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    /**
     * Selection`s filters
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ProfilesBackgroundsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, ProfilesBackgroundsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter');

        $this->order_by = [
            'column' => 'created_at',
            'dir' => Arr::get($inputs, 'order_by_dir')
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
        $instance = ProfilesBackground::query();

        if (!empty($this->filter)) {
            if (!empty($this->filter['user'])) {
                $instance->whereHas('user', function ($query) {
                    $query->where('nickname', 'like', $this->filter['user'] . '%');
                });
            }

            if (isset($this->filter['custom'])) {
                if (!empty($this->filter['custom'])) {
                    $instance->whereNotNull('user_id');
                } else {
                    $instance->whereNull('user_id');
                }

            }

            if (isset($this->filter['available'])) {
                $instance->where('available', $this->filter['available']);
            }
        }

        return $instance;
    }

    /**
     * @param $rootValue
     * @param ProfilesBackgroundsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, ProfilesBackgroundsTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
