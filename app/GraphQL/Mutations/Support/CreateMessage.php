<?php

namespace App\GraphQL\Mutations\Support;

use App\Events\Support\SupportUserMessageCreated;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\NotificationsHelper;
use App\Http\Requests\Support\CreateSupportMessageRequest;
use App\Models\Support;
use App\Models\SupportMessage;
use App\Notifications\Support\SupportMessageCreated;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateMessage
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateSupportMessageRequest $args
     * @param GraphQLContext $context
     * @return SupportMessage
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateSupportMessageRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $support = Support
            ::where('id', $inputs['support_id'])
            ->firstOrFail();

        if (!$user->can('view', $support)) {
            throw new GraphQLLogicRestrictException(__('support.access_denied'), __('Error!'));
        }

        if (in_array($support->status, [Support::STATUS_CLOSED, Support::STATUS_PENDING])) {
            throw new GraphQLLogicRestrictException(__('support.incorrect_support_status'), __('Error'));
        }

        $message = new SupportMessage();
        $message->user_id = $user->id;

        $message->fill($inputs);

        if ($user->id === $support->user_id) {
            $support->unviewed_by_moderator = true;
        } else {
            $support->unviewed_by_user = true;
        }

        if (!$support->messages()->save($message) || !$support->save()) {
            throw new GraphQLSaveDataException(__('support.create_support_message_failed'), __('Error'));
        }

        if ($user->id === $support->moderator_id) {
            $support->user->notify(new SupportMessageCreated($message, $support));
        } else {
            event(new SupportUserMessageCreated($support, $message));
        }

        return $message;
    }
}
