<?php

namespace App\GraphQL\Queries\Auction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\BlockHelper;
use App\Http\Requests\Auction\AuctionsProfileRequest;
use App\Http\Requests\General\IDRequiredRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Auction;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ProfileAuctions extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Page owner`s ID
     *
     * @var integer|string
     */
    protected $page_owner_id;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  AuctionsProfileRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, AuctionsProfileRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->page_owner_id = Arr::get($inputs, 'id');

        $user = $context->user();
        $user->checkProfileAccessibility($this->page_owner_id);

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            [
                'column' => 'end_at',
                'dir' => 'ASC',
            ],
            [
                'column' => 'id',
                'dir' => 'DESC',
            ]
        ];

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get selection base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return Auction
            ::where('user_id', $this->page_owner_id)
            ->active();
    }

    /**
     * Get results total count for base query
     *
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return int
     */
    protected function getTotal($rootValue, IDRequiredRequest $args)
    {
        $inputs = $args->validated();

        $this->page_owner_id = Arr::get($inputs, 'id');

        return $this->getResultsTotalCount();
    }
}
