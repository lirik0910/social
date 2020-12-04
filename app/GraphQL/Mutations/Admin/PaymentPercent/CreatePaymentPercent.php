<?php

namespace App\GraphQL\Mutations\Admin\PaymentPercent;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\PaymentPercent\PaymentPercentRequest;
use App\Models\PaymentPercent;
use App\Traits\DynamicValidation;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreatePaymentPercent
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param PaymentPercentRequest $args
     * @param GraphQLContext $context
     * @return PaymentPercent
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, PaymentPercentRequest $args, GraphQLContext $context)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('payment_percent', $user);

        $inputs = $args->validated();

        $oldPercent = PaymentPercent
            ::where([
                'model' => $inputs['model'],
                'type' => $inputs['type'],
                'percent' => $inputs['percent']
            ])
            ->exists();

        if ($oldPercent) {
            throw new GraphQLLogicRestrictException(__('payment.percent_already_exists'), __('Error'));
        }

        $newPercent = new PaymentPercent();
        $newPercent->fill($inputs);

        if (!$newPercent->save()) {
            throw new GraphQLSaveDataException(__('payment.percent_save_failed'), __('Error'));
        }

        return $newPercent;
    }
}
