<?php

namespace App\GraphQL\Queries\Favorites;

use App\Http\Requests\Favorites\FavoritesRequest;
use App\Http\Requests\Favorites\FavoritesTotalRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\SubscriberUserPublications;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Favorites extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Publications types array for filtering
     *
     * @var array
     */
    protected $filtered_types;

    /**
     * @param $rootValue
     * @param FavoritesRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    protected function resolve($rootValue, FavoritesRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);
        $this->filtered_types = Arr::get($inputs, 'type');

        return [
            'results' => $this->getResults()
        ];
    }

    /**
     * Get favorites selection query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        $subscribes = $this->user->subscribes;

        $subscribes_ids = $subscribes->pluck('id')->toArray();

        $instance = SubscriberUserPublications
            ::whereIn('owner_id', $subscribes_ids)
            ->whereHas('owner', function ($query) {
                $query->whereRaw('(flags & ' . User::FLAG_PRIVATE_PROFILE . ') = 0');
            });

        if(!empty($this->filtered_types)) {
            $instance->whereIn('pub_type', $this->filtered_types);
        }

        return $instance;
    }

    /**
     * @param $rootValue
     * @param FavoritesTotalRequest $args
     * @return int
     */
    protected function getTotal($rootValue, FavoritesTotalRequest $args)
    {
        $this->user = Auth::user();

        $this->filtered_types = Arr::get($args->validated(), 'type');

        return $this->getResultsTotalCount();
    }
}
