<?php

namespace App\GraphQL\Queries\MediaPresent;

use App\Http\Requests\General\IDRequiredRequest;
use App\Http\Requests\MediaPresent\MediaPresentsRequest;
use App\Libraries\GraphQL\AbstractSelection;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Media;

class MediaPresents extends AbstractSelection
{
    use DynamicValidation;

    /**
     * Media
     *
     * @var Media
     */
    protected $media;

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
     * @param  MediaPresentsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, MediaPresentsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();
        $media_id = Arr::get($inputs, 'media_id');
        $this->pagination = Arr::only($inputs, ['limit', 'offset']);

        $this->media = Media
            ::whereId($media_id)
            ->firstOrFail(); ;

        return [
            'results' => $this->getResults(),
            'total_cost' => $this->media->presents_cost
        ];
    }

    /**
     * Get base query instance
     *
     * @return mixed
     */
    public function getBaseQuery()
    {
        $instance = $this->media
            ->presents();

        if($this->user->id !== $this->media->user_id) {
            $instance
                ->where('user_id', $this->user->id);

        }

        return $instance;
    }

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @return int
     */
    protected function getTotal($rootValue, IDRequiredRequest $args)
    {
        $this->user = Auth::user();

        $media_id = Arr::get($args->validated(), 'id');

        $this->media = Media
            ::whereId($media_id)
            ->firstOrFail(); ;

        return $this->getResultsTotalCount();
    }
}
