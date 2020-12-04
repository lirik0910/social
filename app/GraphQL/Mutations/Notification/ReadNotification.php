<?php

namespace App\GraphQL\Mutations\Notification;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Notification\ReadNotificationRequest;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ReadNotification
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ReadNotificationRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return boolean
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ReadNotificationRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $notification_id = Arr::get($args->validated(), 'id');

        $user = $context->user();

        $notification = $user
            ->notifications()
            ->whereId($notification_id)
            ->firstOrFail();

        $notification->markAsRead();

        if(!$notification->read()) {
            throw new GraphQLSaveDataException(__('notification.failed_to_mark_as_read'), __('Error!'));
        }

        return true;
    }
}
