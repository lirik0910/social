<?php

namespace App\GraphQL\Mutations\Admin\PaymentPercent;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\PaymentPercent;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeletePaymentPercent
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param IDRequiredRequest $args
     * @param GraphQLContext $context
     * @return PaymentPercent
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, IDRequiredRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('payment_percent', $user);

        $inputs = $args->validated();

        $percent = PaymentPercent::where('id', $inputs['id'])->firstOrFail();

        if (!$percent) {
            throw new GraphQLLogicRestrictException(__('payment.percent_not_found'), __('Error'));
        }

        if (!$percent->delete()) {
            throw new GraphQLSaveDataException(__('payment.percent_delete_failed'), __('Error'));
        }

        return $percent;
    }
}
