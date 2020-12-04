<?php


namespace App\GraphQL\Mutations\UserActions;


use App\Helpers\NotificationsHelper;
use App\Http\Requests\User\UserDeviceRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\DynamicValidation;
use Edujugon\PushNotification\PushNotification;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AddUserDevice
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param UserDeviceRequest $args
     * @param GraphQLContext $context
     *
     * @return integer
     */
    protected function resolve($rootValue, UserDeviceRequest $args, GraphQLContext $context)
    {
        $inputs = $args->validated();

        UserDevice
            ::where('token', '=', $inputs['device_token'])
            ->delete();

        return \DB::table('user_devices')
            ->updateOrInsert(
                [
                    'user_id' => $context->user()->id,
                    'device_id' => $inputs['device_id']
                ],
                ['device_token' => $inputs['device_token']]
            );
    }

    // TODO remove after debbuging
    public function testNotification ($rootValue, array $args, GraphQLContext $context)
    {
        NotificationsHelper::pushNotification($context->user(), ['ds'=>1]);

        return true;

    }
}
