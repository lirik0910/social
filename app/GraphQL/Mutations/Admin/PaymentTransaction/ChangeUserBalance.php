<?php

namespace App\GraphQL\Mutations\Admin\PaymentTransaction;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\PaymentTransaction\ChangeUserBalanceRequest;
use App\Models\AdminPaymentTransaction;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ChangeUserBalance
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ChangeUserBalanceRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ChangeUserBalanceRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $admin_user = $context->user();

        AdminPermissionsHelper::check('user_balance_change', $admin_user);

        $inputs = $args->validated();

        $user_id = Arr::get($inputs, 'id');
        $amount = Arr::get($inputs, 'amount');
        $type = Arr::get($inputs, 'type');

        $user = User
            ::whereId($user_id)
            ->firstOrFail();

        if ($user->role !== User::ROLE_USER) {
            throw new GraphQLLogicRestrictException(__('user.incorrect_role'), __('Error!'));
        }

        $admin_transaction = new AdminPaymentTransaction();
        $admin_transaction->user_id = $user->id;
        $admin_transaction->admin_id = $admin_user->id;
        $admin_transaction->amount = $amount;
        $admin_transaction->type = $type;

        if (!$admin_transaction->save()) {
            throw new GraphQLSaveDataException(__('payment_transaction.update_failed'), __('Error!'));
        }

        return $admin_transaction;
    }
}
