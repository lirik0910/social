<?php

namespace App\GraphQL\Queries\Common;

use App\Exceptions\GraphQLLogicRestrictException;
use App\GraphQL\Queries\Advert\UserAdverts;
use App\Helpers\ChatRoomHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\MediaHelper;
use App\Helpers\PhoneHelper;
use App\Models\Advert;
use App\Models\CharityOrganization;
use App\Models\Media;
use App\Models\Meeting;
use App\Models\PaymentOrder;
use App\Models\PaymentTransaction;
use App\Models\Profile;
use App\Models\Report;
use App\Models\Support;
use App\Models\User;
use App\Models\WantWithYou;
use App\Models\Auction;
use App\Models\UsersPrivateChatRoom;
use App\Models\UsersPrivateChatRoomMessage;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\GraphQL\Queries\User\SearchUsers;
use App\GraphQL\Queries\Auction\SearchAuctions;
use App\GraphQL\Queries\Meeting\MeetingsHistory;
use App\GraphQL\Queries\Auction\AuctionsHistory;

class Settings
{
    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return [
            'phoneCodes' => PhoneHelper::phoneAssoc(),
            'flags' => [
                'requiredPhoneVerification' => User::FLAG_REQUIRED_PHONE_VERIFICATION,
                'requiredFillProfile' => User::FLAG_REQUIRED_FILL_PROFILE,
                'privateProfile' => User::FLAG_PRIVATE_PROFILE,
                'enablePhoneVerification' => User::FLAG_ENABLED_PHONE_VERIFICATION,
                'photoVerifiedPending' => User::FLAG_PHOTO_VERIFIED_PENDING,
                'photoVerified' => User::FLAG_PHOTO_VERIFIED,
                'onLineStatus' => User::FLAG_USER_ONLINE,
                'banned' => User::FLAG_USER_BANNED,
            ],
            'roles' => array_flip(User::availableParams('role')),
            'profile' => [
                'gender' => Profile::availableParams('gender', 'profile.gender'),
                'preferences' => Profile::availableParams('preference', 'profile.preference'),
                'physique' => Profile::availableParams('physique', 'profile.physique'),
                'appearance' => Profile::availableParams('appearance', 'profile.appearance'),
                'eye_color' => Profile::availableParams('eye_color', 'profile.eye_color'),
                'hair_color' => Profile::availableParams('hair_color', 'profile.hair_color'),
                'marital_status' => Profile::availableParams('marital_status', 'profile.marital_status'),
                'smoking' => Profile::availableParams('smoking', 'profile.smoking'),
                'alcohol' => Profile::availableParams('alcohol', 'profile.alcohol'),
                'kids' => Profile::availableParams('kids', 'profile.kids'),
                'languages' => LanguageHelper::languageList()
            ],
            'media' => [
                'type' => array_flip(Media::availableParams('type')),
                'status' => array_flip(Media::availableParams('status')),
            ],
            'want_with_you_types' => array_flip(WantWithYou::availableParams('type')),
            'upload_file_types' => array_flip(MediaHelper::availableParams('file_type')),
            'order_by' => [
                'search_users' => SearchUsers::availableParams('order_by', 'settings.type'),
                'search_auctions' => SearchAuctions::availableParams('order_by', 'settings.type'),
            ],
            'advert' => [
                'type' => array_flip(Advert::availableParams('type')),
                'status' => array_flip(UserAdverts::availableParams('status'))
            ],
            'room' => [
                'view_type' => array_flip(UsersPrivateChatRoom::availableParams('view_type')),
                'event_type' => array_flip(ChatRoomHelper::availableParams('chat_room_event')),
                'message_type' => [
                    'default' => UsersPrivateChatRoomMessage::TYPE_MESSAGE,
                    'price_changed' => UsersPrivateChatRoomMessage::TYPE_PRICE_CHANGED,
                ],
            ],
            'meetings' => [
                'type' => array_flip(MeetingsHistory::availableParams('type')),
                'status' => array_flip(Meeting::availableParams('status')),
                'outfit' => Meeting::availableParams('outfit', 'meeting.outfit'),
            ],
            'auctions' => [
                'type' => array_flip(AuctionsHistory::availableParams('type')),
                'status' => array_flip(Auction::availableParams('status')),
                'outfit' => Meeting::availableParams('outfit', 'meeting.outfit'),
            ],
            'reports' => Report::getSettings(),
            'reports_status' => array_flip(Report::availableParams('status')),
            'thumbs_sizes' => MediaHelper::getThumbSizes(),
            'payment_transactions' => [
                'status' => array_flip(PaymentTransaction::availableParams('transaction_status')),
                'type' => array_flip(PaymentTransaction::availableParams('transaction_type')),
                'source_type' => array_flip(PaymentTransaction::availableParams('transaction_source_type')),
            ],
            'support' => [
                'status' => array_flip(Support::availableParams('status')),
                'categories' => Support::availableParams('category', 'support.category'),
            ],
            'payment_order' => [
                'type' => array_flip(PaymentOrder::availableParams('order_type')),
                'status' => array_flip(PaymentOrder::availableParams('order_status')),
            ],
            'charity_organization' => [
                'moderation_status' => array_flip(CharityOrganization::availableParams('moderation_status')),
                'moderation_declined_reason' => CharityOrganization::availableParams('moderation_declined_reason', 'charity_organization.moderation_declined_reason'),
            ],
        ];
    }
}
