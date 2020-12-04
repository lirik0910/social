<?php

namespace App\GraphQL\Mutations\Call;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Call\CreateAnswerRequest;
use App\Models\UsersPrivateCall;
use App\Traits\DynamicValidation;
use App\Events\CallMessageEvent;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateAnswer
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param CreateAnswerRequest $args
     * @param GraphQLContext $context
     * @return UsersPrivateCall
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateAnswerRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        $call = UsersPrivateCall::whereId($inputs['parent_id'])->whereNull('parent_id')->firstOrFail();

        $answer = new UsersPrivateCall();
        $answer->fill($inputs);

        if ($inputs['action'] != UsersPrivateCall::ACTION_MISS) {
            $call->status = true;
            if (!$call->save()) {
                throw new GraphQLSaveDataException(__('call.change_status_filed'), __('Error'));
            }
        }

        if (!$answer->save()) {
            throw new GraphQLSaveDataException(__('call.create_answer_filed'), __('Error'));
        }

        $message = [
            'id' => $call->id,
            'caller_user_id' => $call->caller_user_id,
            'callee_user_id' => $call->callee_user_id,
            'action' => $answer->action,
            'meeting_id' => $call->meeting_id
        ];
        event(new CallMessageEvent($message, $call->callee_user_id));

        return $answer;
    }
}
