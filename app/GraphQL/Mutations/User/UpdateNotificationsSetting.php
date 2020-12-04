<?php

namespace App\GraphQL\Mutations\User;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\NotificationsHelper;
use App\Http\Requests\User\UpdateNotificationsSettingRequest;
use App\Models\User;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateNotificationsSetting
{
    use DynamicValidation;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * @param $rootValue
     * @param UpdateNotificationsSettingRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return User|\Illuminate\Foundation\Auth\User|null
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, UpdateNotificationsSettingRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $this->user = $context->user();

        $user_settings = NotificationsHelper::compareTreeStructure($this->user);

        if (isset($user_settings[$inputs['type']][$inputs['notification']])) {
            $user_settings[$inputs['type']][$inputs['notification']] = $inputs['value'];
        }

        $this->user->notifications_settings = $user_settings;

        if (!$this->user->save()) {
            throw new GraphQLSaveDataException(__('Save data failed'), __('Error'));
        }

        return $this->user->notifications_settings;
    }


}
