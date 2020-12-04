<?php

namespace App\GraphQL\Mutations\Admin\PaymentPercent;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\PaymentPercent\EditPaymentPercentRequest;
use App\Models\PaymentPercent;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EditPaymentPercent
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param EditPaymentPercentRequest $args
     * @param GraphQLContext $context
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, EditPaymentPercentRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('payment_percent', $user);

        $inputs = $args->validated();

        $percent = PaymentPercent::where('id', $inputs['id'])->firstOrFail();

        if (!$percent) {
            throw new GraphQLLogicRestrictException(__('payment.percent_not_found'), __('Error'));
        }

        $percent->fill($inputs);

        if (!$percent->save()) {
            throw new GraphQLSaveDataException(__('payment.percent_update_failed'), __('Error'));
        }

        return $percent;
    }
}
