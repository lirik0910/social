<?php

namespace App\GraphQL\Queries\Support;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\General\IDRequiredRequest;
use App\Http\Requests\Support\SupportMessagesRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Support;
use App\Models\SupportMessage;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SupportMessages extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Support ID
     *
     * @var integer|string
     */
    protected $support_id;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param SupportMessagesRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, SupportMessagesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $this->support_id = Arr::get($inputs, 'support_id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->order_by = [
            'dir' => 'DESC',
            'column' => 'created_at'
        ];

        $support = Support
            ::where('id', $this->support_id)
            ->firstOrFail();

        if(!$this->user->can('view', $support)){
            throw new GraphQLSaveDataException(__('support.support_access_denied'), __('Error'));
        }

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * @return mixed
     */
    public function getBaseQuery()
    {
        return SupportMessage
            ::where('support_id', $this->support_id);
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
        $this->support_id = Arr::get($args->validated(), 'id');

        return $this->getResultsTotalCount();
    }
}
