<?php

namespace App\GraphQL\Queries\Admin\User;

use App\Http\Requests\Admin\User\UserVerificationPhotosRequest;
use App\Http\Requests\Admin\User\UserVerificationPhotosTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\UserPhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserVerificationPhotos extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Filter for querying
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UserVerificationPhotosRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, UserVerificationPhotosRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'column' => 'created_at',
            'dir' => Arr::get($inputs, 'order_by_dir')
        ];

        $this->filter = Arr::get($inputs, 'filter');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return base query instance
     *
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function getBaseQuery()
    {
        $instance = UserPhotoVerification::has('media');

        if (!empty($this->filter['nickname'])) {
            $instance->whereHas('user', function ($query) {
                $query->where('nickname', 'like', $this->filter['nickname'] . '%');
            });
        }

        if (!empty($this->filter['status'])) {
            $instance->where('status', $this->filter['status']);
        } else {
            $instance->where('status', '!=', UserPhotoVerification::STATUS_NEW);
        }

        if (!empty($this->filter['decline_reason'])) {
            $instance->where('decline_reason', $this->filter['decline_reason']);
        }

        if (!empty($this->filter['date_period'])) {
            $instance
                ->whereDate('created_at', '>=', $this->filter['date_period']['from'])
                ->whereDate('created_at', '<=', $this->filter['date_period']['to']);
        } elseif (!empty($this->filter['date'])) {
            $instance->whereDate('created_at', $this->filter['date']);
        }

        return $instance;
    }

    /**
     * Return total count for base query
     *
     * @param $rootValue
     * @param UserVerificationPhotosTotalRequest $args
     * @return integer
     */
    protected function getTotal($rootValue, UserVerificationPhotosTotalRequest $args)
    {
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
