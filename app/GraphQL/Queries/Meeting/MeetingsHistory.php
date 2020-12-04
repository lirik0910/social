<?php


namespace App\GraphQL\Queries\Meeting;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Http\Requests\Charity\CharityFilterRequest;
use App\Http\Requests\Meetings\MeetingsHistoryFilterRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Meeting;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class MeetingsHistory extends AbstractSelection
{
    use DynamicValidation, ReflectionTrait;

    const TYPE_BUY = 1;
    const TYPE_SELL = 2;

    protected $filter;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param MeetingsHistoryFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, MeetingsHistoryFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();
        $this->filter = $args->validated();

        $this->pagination = \Arr::only($this->filter, ['limit', 'offset']);
        $this->order_by = [
            ['column' => 'updated_at', 'dir' => 'DESC'],
            ['column' => 'id', 'dir' => 'ASC'],
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get base query for user meetings selection
     *
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    public function getBaseQuery()
    {

        $base_query = Meeting::where(
            $this->filter['type'] == self::TYPE_BUY ? 'user_id' : 'seller_id',
            $this->user->id
        );

        if ($this->filter['status']) {
            $base_query->where('status', $this->filter['status']);
        }

        return $base_query;
    }

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param MeetingsHistoryFilterRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function getFilterTotal($rootValue, MeetingsHistoryFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = \Auth::user();
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }

}
