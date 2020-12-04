<?php

namespace App\GraphQL\Mutations\Support;

use App\Events\Support\SupportChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Admin\Support\ChangeSupportStatusRequest;
use App\Models\Support;
use App\Traits\DynamicValidation;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeSupportStatus
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param ChangeSupportStatusRequest $args
     * @param GraphQLContext $context
     * @return Support
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ChangeSupportStatusRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');
        $status = Arr::get($inputs,'status');

        $support = Support
            ::where('id', $id)
            ->firstOrFail();

        if (!$user->can('changeStatus', $support)) {
            throw new GraphQLLogicRestrictException(__('support.access_denied'), __('Error!'));
        }

        if ($support->status === Support::STATUS_CLOSED && $status !== Support::STATUS_IN_PROGRESS) {
            throw new GraphQLLogicRestrictException(__('support.incorrect_status_change_for_ended'), __('Error'));
        }

        if (is_null($support->moderator_id)) {
            $support->moderator_id = $user->id;
            $support->unviewed_by_moderator = false;
        }

        $support->fill($inputs);

        if (!$support->save()) {
            throw new GraphQLSaveDataException(__('support.update_failed'), __('Error'));
        }

        event(new SupportChanged($support, ['status' => $support->status]));

        return $support;
    }
}
