<?php

namespace App\GraphQL\Mutations\Support;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Support\CreateSupportRequest;
use App\Models\Support;
use App\Models\SupportMessage;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateSupport
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateSupportRequest $args
     * @param GraphQLContext $context
     * @return array
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateSupportRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $support = new Support();
        $support->user_id = $user->id;
        $support->category = $inputs['category'];

        if (!$support->save()) {
            throw new GraphQLSaveDataException(__('support.create_support_failed'), __('Error'));
        }

        $message = new SupportMessage();
        $message->support_id = $support->id;
        $message->user_id = $user->id;
        $message->message = $inputs['message'];

        if (!$message->save()) {
            throw new GraphQLSaveDataException(__('support.create_support_message_failed'), __('Error'));
        }

        return [
            'support' => $support,
            'message' => $message,
        ];
    }
}
