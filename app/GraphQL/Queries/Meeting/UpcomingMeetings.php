<?php

namespace App\GraphQL\Queries\Meeting;

use App\Http\Requests\General\PaginationRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Meeting;

class UpcomingMeetings extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  PaginationRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, PaginationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();

        $this->pagination = Arr::only($args->validated(), ['limit', 'offset']);
        $this->order_by = [
            'dir' => 'ASC',
            'column' => 'meeting_date'
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Return base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return Meeting
            ::where(function ($query) {
                $query
                    ->where('user_id', $this->user->id)
                    ->orWhere('seller_id', $this->user->id);
            })
            ->where('status', Meeting::STATUS_ACCEPTED)
            ->whereDate('meeting_date', DB::raw('CURDATE()'));
    }

    /**
     * Return base query results total count
     *
     * @return int
     */
    public function getTotal()
    {
        $this->user = Auth::user();

        return $this->getResultsTotalCount();
    }
}
