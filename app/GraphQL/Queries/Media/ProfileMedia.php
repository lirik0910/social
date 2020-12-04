<?php

namespace App\GraphQL\Queries\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\BlockHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Http\Requests\Media\MediaProfileRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Media;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Arr;

class ProfileMedia extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Page owner`s ID
     *
     * @var integer|string
     */
    protected $page_owner_id;

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
     * @param  MediaProfileRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, MediaProfileRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->page_owner_id = Arr::get($inputs, 'id');

        $this->user = $context->user();
        $this->user->checkProfileAccessibility($this->page_owner_id);

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);

        return [
            'results' => $this->getResults()
        ];
    }

    public function getBaseQuery()
    {
        return Media
            ::where(function ($query) {
                $query->where('type', '!=', Media::TYPE_AVATAR);
                $query->where('user_id', $this->page_owner_id);

                if($this->page_owner_id !== $this->user->id) {
                    $query->notBanned();
                }
            });
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

        $this->user = Auth::user();
        $this->page_owner_id = Arr::get($inputs, 'id');

        return $this->getResultsTotalCount();
    }
}
