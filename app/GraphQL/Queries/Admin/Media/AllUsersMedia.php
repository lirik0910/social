<?php

namespace App\GraphQL\Queries\Admin\Media;

use App\Http\Requests\Admin\Media\AllUsersMediaRequest;
use App\Http\Requests\Admin\Media\AllUsersMediaTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Media;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AllUsersMedia extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

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
     * @param  AllUsersMediaRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AllUsersMediaRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $order_by = Arr::get($inputs, 'order_by');
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
     * Return base query instance for selection
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function getBaseQuery()
    {
        $instance = Media::query();

        if (!empty($this->filter['nickname'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', 'like', $this->filter['nickname'] . '%');
            });
        }

        if (!empty($this->filter['mimetype'])) {
            $instance->where('mimetype', $this->filter['mimetype']);
        }

        if (isset($this->filter['active'])) {
            if (!empty($this->filter['active'])) {
                $instance
                    ->where('status', '!=', Media::STATUS_BANNED)
                    ->orWhereNull('status');
            } else {
                $instance->where('status', '=', Media::STATUS_BANNED);
            }

        }

        if (!empty($this->filter['type'])) {
            $instance->where('type', '=', $this->filter['type']);
        }

        return $instance;
    }

    /**
     * Return selection total count for base query
     *
     *
     * @param $rootValue
     * @param AllUsersMediaTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, AllUsersMediaTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
