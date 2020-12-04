<?php

namespace App\GraphQL\Queries\Advert;

use App\Http\Requests\User\Adverts\UserAdvertsRequest;
use App\Http\Requests\User\Adverts\UserAdvertsTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserAdverts extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const STATUS_ALL = 1;
    const STATUS_CURRENT = 2;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Filter params
     *
     * @var array
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UserAdvertsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, UserAdvertsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filter = Arr::get($inputs, 'filter', []);

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get base query instance for adverts selection
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = $this->user
            ->adverts();

        if(!empty($this->filter)) {
            $instance->where(function ($query) {
                if(!empty($this->filter['type'])) {
                    $query->where('type', $this->filter['type']);
                }

                if(!empty($this->filter['status']) && $this->filter['status'] === self::STATUS_CURRENT) {
                    $query->whereNull('respond_id');
                    $query->whereNull('cancelled_at');
                    $query->where('end_at', '>', \DB::raw('NOW()'));
                }
            });
        }

        return $instance;
    }

    /**
     * Return results total count for base query
     *
     * @param $rootValue
     * @param UserAdvertsTotalRequest $args
     * @return mixed
     */
    protected function getTotal($rootValue, UserAdvertsTotalRequest $args)
    {
        $inputs = $args->validated();

        $this->user = Auth::user();

        $this->filter = Arr::only($inputs, ['type', 'status']);

        return $this->getResultsTotalCount();
    }
}
