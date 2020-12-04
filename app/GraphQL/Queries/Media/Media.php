<?php

namespace App\GraphQL\Queries\Media;

use App\Http\Requests\General\IDRequiredRequest;
use App\Models\MediaUsersView;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Media as Model;

class Media
{
    use DynamicValidation;

    /**
     * Queried media
     *
     * @var Model
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
     * @param  IDRequiredRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $this->user = $context->user();

        $media_id = Arr::get($args->validated(), 'id');

        $this->media = Model
            ::whereId($media_id)
            ->firstOrFail();

        if($this->user->id !== $this->media->user_id) {
            $this->updateViews();
        }

        return $this->media;
    }

    /**
     * Update views count for media
     */
    protected function updateViews()
    {
        $userView = MediaUsersView
            ::where('media_id', $this->media->id)
            ->where('user_id', $this->user->id)
            ->first();

        if (!$userView) {
            $this->media
                ->users_views()
                ->create(['user_id' => $this->user->id]);

            $this->media->increment('views');
        }
    }
}
