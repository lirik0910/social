<?php

namespace App\GraphQL\Queries\User;

use App\Helpers\BlockHelper;
use App\Http\Requests\User\UserRequest;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\User as Model;

class User
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var Model
     */
    protected $auth_user;

    /**
     * Queried user
     *
     * @var Model
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UserRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    protected function resolve($rootValue, UserRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $slug = Arr::get($args->validated(), 'slug');

        $this->auth_user = $context->user();

        if($this->auth_user->slug !== $slug) {
            $this->user = Model
                ::where('slug', $slug)
                ->select([
                    'users.*',
                    'blocked_users.id as blocked'
                ])
                ->leftJoin('blocked_users', function ($join) {
                    $join
                        ->on('blocked_users.user_id', '=', 'users.id')
                        ->where('blocked_id', $this->auth_user->id);
                })
                ->first();

            if($this->user) {
                if($this->checkVisibility()) {
                    $this->user->show_private_avatar = true;
                    $this->user->removeFlag(Model::FLAG_PRIVATE_PROFILE);
                } else {
                    if (!$this->user->hasFlag(Model::FLAG_PRIVATE_PROFILE)) {
                        $this->user->addFlag(Model::FLAG_PRIVATE_PROFILE);
                    }
                }
            }
        } else {
            $this->user = $this->auth_user;
        }

        return $this->user;
    }

    /**
     * Determine if the whether user profile visible
     *
     * @return bool
     */
    protected function checkVisibility()
    {
        $is_hidden = $this->user->hasFlag(Model::FLAG_PRIVATE_PROFILE) && empty(BlockHelper::checkCurrentEventsExists($this->auth_user, $this->user));
        $is_blocked = !empty($this->user->blocked);

        return !$is_hidden && !$is_blocked;
    }
}
