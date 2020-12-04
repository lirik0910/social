<?php

namespace App\GraphQL\Queries\User;

use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserBlocksById extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param PaginationRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return Collection|mixed
     */
    protected function resolve($rootValue, PaginationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->order_by = [
            'column' => 'created_at',
            'dir' => 'DESC'
        ];

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $blocked_users_records = $this->getResults();

        $blocked_users = $blocked_users_records->map(function ($record, $key) {
            return $record->blocked_user;
        });

        return [
            'results' => $blocked_users
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|mixed
     */
    public function getBaseQuery()
    {
        return $this->user
            ->blocked_users()
            ->where('blocked_by_phone', false)
            ->with('blocked_user');
    }

    /**
     * @return int
     */
    protected function getTotal()
    {
        $this->user = Auth::user();

        return $this->getResultsTotalCount();
    }
}
