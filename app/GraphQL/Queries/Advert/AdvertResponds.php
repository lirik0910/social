<?php

namespace App\GraphQL\Queries\Advert;

use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Advert\AdvertRespondsRequest;
use App\Http\Requests\Advert\AdvertRespondsTotalRequest;
use App\Http\Requests\General\IDRequiredRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\Advert;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdvertResponds extends AbstractSelection
{
    use DynamicValidation;

    /**
     * @var Advert
     */
    protected $advert;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  AdvertRespondsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, AdvertRespondsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $query = Advert::whereId($inputs['id']);

        if ($user->role === User::ROLE_USER) {
            $query->where('user_id', $user->id);
        } else {
            AdminPermissionsHelper::check('advert_info', $user);
        }

        $this->advert = $query->firstOrFail();

        $this->pagination = Arr::only($inputs, ['limit', 'offset']);

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get base query instance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|mixed
     */
    public function getBaseQuery()
    {
        return $this->advert
            ->responds()
            ->withPivot(['created_at']);
    }

    /**
     * Get base query results total count
     *
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return mixed
     */
    protected function getTotal($rootValue, IDRequiredRequest $args)
    {
        $inputs = $args->validated();

        $this->advert = Advert
            ::whereId($inputs['id'])
            ->firstOrFail();

        return $this->getResultsTotalCount();
    }
}
