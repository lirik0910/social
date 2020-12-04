<?php

namespace App\GraphQL\Mutations\Call;

use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Call\ChangeStatusRequest;
use App\Models\UsersPrivateCall;
use App\Traits\DynamicValidation;

class ChangeStatus
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ChangeStatusRequest $args
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ChangeStatusRequest $args)
    {
        $inputs = $args->validated();

        $call = UsersPrivateCall::whereId($inputs['id'])->whereNull('parent_id')->firstOrFail();

        $call->status = true;

        if (!$call->save()) {
            throw new GraphQLSaveDataException(__('meeting.change_status_failed'), __('Error'));
        }

        return $call;
    }
}
