<?php

namespace App\GraphQL\Queries\Chat;

use App\Http\Requests\Chat\RoomPaymentsTotalRequest;
use App\Http\Requests\Chat\RoomsPaymentsRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Traits\DynamicValidation;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use Auth;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RoomsPayments extends AbstractSelection
{
    use DynamicValidation;

    protected $filter;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param RoomsPaymentsRequest $args
     * @param GraphQLContext $context
     * @return array
     */
    protected function resolve($rootValue, RoomsPaymentsRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->filter = Arr::get($inputs, 'filter');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'dir' => 'DESC',
            'column' => 'updated_at'
        ];
//var_dump($this->filter); die;
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
        $instance = UsersPrivateChatRoom
            ::where(function ($query) {
                $query
                    ->where('user_id', '=', $this->user->id)
                    ->orWhere('seller_id', '=', $this->user->id);
            })
            ->where('amount', '>', 0);

        if (isset($this->filter['ended'])) {
            if (!empty($this->filter['ended'])) {
                $instance
                    ->whereNotNull('ended_by_id')
                    ->whereNotNull('ended_at');
            } else {
                $instance
                    ->whereNull('ended_by_id')
                    ->whereNull('ended_at');
            }
        }

        return $instance;
    }

    /**
     * Get results total count for base query
     *
     * @param $rootValue
     * @param RoomPaymentsTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, RoomPaymentsTotalRequest $args)
    {
        $this->user = Auth::user();
        $this->filter = $args->validated();

        return $this->getResultsTotalCount();
    }
}
