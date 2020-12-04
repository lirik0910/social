<?php

namespace App\GraphQL\Mutations\Notification;

use App\Exceptions\GraphQLSaveDataException;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ReadNotifications
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return boolean
     * @throws GraphQLSaveDataException
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        if(!$user->unreadNotifications()->update(['read_at' => Carbon::now()])) {
            throw new GraphQLSaveDataException(__('notification.failed_to_mark_as_read'), __('Error!'));
        }

        return true;
    }
}
