<?php

namespace App\GraphQL\Queries\Admin\Common;

use App\Helpers\LogsHelper;
use App\Models\AdminPaymentTransaction;
use App\Models\GlobalLog;
use App\Models\Report;
use App\Models\Support;
use App\Models\User;
use App\Models\UserPhotoVerification;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdminSettings
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return [
            'users' => [
                'role' => array_flip(User::availableParams('role')),
                'permissions' => array_flip(User::availableParams('permission')),
            ],
            'verification_requests' => [
                'status' => array_flip(UserPhotoVerification::availableParams('status')),
                'decline_reason' => UserPhotoVerification::availableParams('decline_reason', 'verification_request.decline_reason')
            ],
            'adminPaymentTransactions' => [
                'type' => array_flip(AdminPaymentTransaction::availableParams('type')),
            ],
            'logs' => [
                'mutations' => LogsHelper::getLogsSettings(),
                'sections' => array_flip(GlobalLog::availableParams('admin_section', 'logs.admin_section')),
            ],
            'support_permissions' => [
                Support::CATEGORY_PAYMENT => User::PERMISSION_SUPPORT_PAYMENT,
                Support::CATEGORY_MEETING => User::PERMISSION_SUPPORT_MEETING,
                Support::CATEGORY_AUCTION => User::PERMISSION_SUPPORT_AUCTION,
                Support::CATEGORY_ADVERT => User::PERMISSION_SUPPORT_ADVERT,
                Support::CATEGORY_FEEDBACK => User::PERMISSION_SUPPORT_FEEDBACK,
                Support::CATEGORY_LOGIN => User::PERMISSION_SUPPORT_LOGIN,
                Support::CATEGORY_ACCOUNT_ACCESS => User::PERMISSION_SUPPORT_ACCOUNT_ACCESS,
                Support::CATEGORY_UPLOAD => User::PERMISSION_SUPPORT_UPLOAD,
                Support::CATEGORY_AVATAR_VERIFICATION => User::PERMISSION_SUPPORT_AVATAR_VERIFICATION,
                Support::CATEGORY_CHARITY_VERIFICATION => User::PERMISSION_SUPPORT_CHARITY_VERIFICATION,
            ],
            'reports_permissions' => [
                Report::TYPE_ADVERTS => User::PERMISSION_REPORT_ADVERT,
                Report::TYPE_AUCTIONS => User::PERMISSION_REPORT_AUCTION,
                Report::TYPE_CHAT_ROOMS => User::PERMISSION_REPORT_CHAT_ROOM,
                Report::TYPE_MEDIA => User::PERMISSION_REPORT_MEDIA,
                Report::TYPE_MEETINGS => User::PERMISSION_REPORT_MEETING,
                Report::TYPE_USERS => User::PERMISSION_REPORT_USER,
            ],
        ];
    }
}
