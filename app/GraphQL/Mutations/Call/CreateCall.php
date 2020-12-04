<?php

namespace App\GraphQL\Mutations\Call;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Call\CreateCallRequest;
use App\Models\UsersPrivateCall;
use App\Traits\DynamicValidation;
use App\Events\CallMessageEvent;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateCall
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateCallRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateCall
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateCallRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $call = new UsersPrivateCall();
        $call->caller_user_id = $context->user()->id;
        $call->action = UsersPrivateCall::ACTION_CALL;
        $call->fill($inputs);

        if (!$call->save()) {
            throw new GraphQLSaveDataException(__('call.create_call_failed'), __('Error'));
        }

        $message = [
            'callee_user_id' => $call->callee_user_id,
            'meeting_id' => $call->meeting_id
        ];

        event(new CallMessageEvent($message, $call->callee_user_id));

        return $call;
    }
}
