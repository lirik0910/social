<?php

namespace App\GraphQL\Queries\Media;

use App\Http\Requests\User\UserMediaFilterRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Media;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserMedia extends AbstractSelection
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
     * @param  UserMediaFilterRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function resolve($rootValue, UserMediaFilterRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->order_by = [
            [
                'column' => 'created_at',
                'dir' => 'DESC'
            ],
            [
                'column' => 'id',
                'dir' => 'DESC'
            ]
        ];

        $this->pagination = $inputs;

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get selection base query
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        return Media
            ::where(function ($query) {
                $query->where('user_id', $this->user->id);
                $query->where('type', '!=', Media::TYPE_AVATAR);
            });
    }

    /**
     * Return total of user media.
     *
     * @return int
     */
    protected function getFilterTotal()
    {
        $this->user = \Auth::user();

        return $this->getResultsTotalCount();
    }
}
