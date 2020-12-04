<?php


namespace App\Helpers;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Libraries\NotificationsSending\SendAdminPaymentTransactionCreatedNotification;
use App\Libraries\NotificationsSending\SendAdvertCancelledNotification;
use App\Libraries\NotificationsSending\SendAdvertCreatedNotification;
use App\Libraries\NotificationsSending\SendAdvertMeetingCreatedNotification;
use App\Libraries\NotificationsSending\SendAdvertRespondCreatedNotification;
use App\Libraries\NotificationsSending\SendAdvertUpdatedNotification;
use App\Libraries\NotificationsSending\SendAuctionBidCreatedNotification;
use App\Libraries\NotificationsSending\SendAuctionBidOutdatedNotification;
use App\Libraries\NotificationsSending\SendAuctionCancelledNotification;
use App\Libraries\NotificationsSending\SendAuctionCreatedNotification;
use App\Libraries\NotificationsSending\SendAuctionEndSoonNotification;
use App\Libraries\NotificationsSending\SendAuctionMeetingCreatedNotification;
use App\Libraries\NotificationsSending\SendAuctionWinnerBannedNotification;
use App\Libraries\NotificationsSending\SendMediaBannedNotification;
use App\Libraries\NotificationsSending\SendMediaCreatedNotification;
use App\Libraries\NotificationsSending\SendMediaPresentCreatedNotification;
use App\Libraries\NotificationsSending\SendMediaVerificationNotification;
use App\Libraries\NotificationsSending\SendMeetingConfirmationCodeNotification;
use App\Libraries\NotificationsSending\SendMeetingCreatedNotification;
use App\Libraries\NotificationsSending\SendMeetingRateNeededNotification;
use App\Libraries\NotificationsSending\SendMeetingStartSoonNotification;
use App\Libraries\NotificationsSending\SendMeetingStatusChangedNotification;
use App\Libraries\NotificationsSending\SendMessageCreatedNotification;
use App\Libraries\NotificationsSending\SendPaymentOrderNotification;
use App\Libraries\NotificationsSending\SendReportsCountLimitReachedNotification;
use App\Libraries\NotificationsSending\SendSubscribeCreatedNotification;
use App\Libraries\NotificationsSending\SendWantWithYouCreatedNotification;
use App\Models\User;
use App\Notifications\AdminPaymentTransactionCreated;
use App\Notifications\Advert\AdvertCancelled;
use App\Notifications\Advert\AdvertCreated;
use App\Notifications\Advert\AdvertRespondCreated;
use App\Notifications\Advert\AdvertUpdated;
use App\Notifications\Auction\AuctionBidCreated;
use App\Notifications\Auction\AuctionBidOutdated;
use App\Notifications\Auction\AuctionCanceled;
use App\Notifications\Auction\AuctionEndSoon;
use App\Notifications\Auction\AuctionCreated;
use App\Notifications\Auction\WinnerBanned;
use App\Notifications\Chat\MessageCreated;
use App\Notifications\Media\MediaBanned;
use App\Notifications\Media\MediaCreated;
use App\Notifications\MediaPresent\MediaPresentCreated;
use App\Notifications\Meeting\AuctionMeetingCreated;
use App\Notifications\Meeting\AdvertMeetingCreated;
use App\Notifications\Meeting\MeetingRateNeeded;
use App\Notifications\Meeting\MeetingStatusChanged;
use App\Notifications\Meeting\MeetingCreated;
use App\Notifications\Meeting\MeetingStartSoon;
use App\Notifications\Media\MediaVerification;
use App\Notifications\Meeting\MeetingConfirmationCode;
use App\Notifications\PaymentOrder\PaymentOrderIn;
use App\Notifications\PaymentOrder\PaymentOrderOut;
use App\Notifications\ReportsCountLimitReached;
use App\Notifications\SubscribeCreated;
use App\Notifications\WantWithYouCreated;
use Illuminate\Support\Arr;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Database\Eloquent\Collection;


class NotificationsHelper
{
    /**
     * Handle notifications array and manually run listeners
     *
     * @param array $notifications
     * @param $object
     * @param $model_name
     */
    public static function handle(array $notifications, $object, $model_name)
    {
        foreach($notifications as $notification) {
            $notification_data = self::getNotification($model_name, $notification);

            self::sendNotification($notification_data, $object);
        }

        return;
    }

    /**
     * Send notification
     *
     * @param array $data
     * @param $object
     * @return mixed
     */
    protected static function sendNotification(array $data, $object)
    {
        $listener_class = $data['listener'] ?? null;
        $notification_class = $data['notification'] ?? null;
        $notification_path = $data['path'];

        if(!empty($listener_class) && !empty($notification_class)) {
            $listener = new $listener_class($notification_class, $notification_path, $object);
            $listener->handle();
        }

        return;
    }

    /**
     * Return notification data
     *
     * @param string $model_name
     * @param string $notification
     * @return mixed
     */
    public static function getNotification(string $model_name, string $notification)
    {
        $notifications = self::getNotifications();

        $path = lcfirst($model_name) . '.' . $notification;

        return Arr::add(Arr::get($notifications, $path), 'path', $path);
    }

    /**
     * Return notifications tree with default values for database field
     *
     * @return array
     */
    public static function getNotificationsTree()
    {
        $available_notifications = self::getNotifications();

        foreach($available_notifications as $type => $notifications) {
            if(!empty($notifications)) {
                data_set($available_notifications, $type . '.*', true);
            }
        }

        return $available_notifications;
    }

    /**
     * Return data about user who initiate notification
     *
     * @param $notification_user
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getNotificationUserData($notification_user)
    {
        if (!empty($notification_user)) {
            $thumbnail_size = MediaHelper::getThumbSizes($notification_user)[1];

            $data = [
                'id' => (string) $notification_user->id,
                'nickname' => $notification_user->nickname,
                'slug' => $notification_user->slug,
                'avatar' => $notification_user->avatar,
                'avatar_thumbs' => $notification_user->getThumbs($notification_user, ['sizes' => [$thumbnail_size]]),
            ];
        }

        return $data ?? null;
    }

    /**
     * Checking user notifications settings field structure and correct if needed
     *
     * @param User $user
     * @return mixed
     */
    public static function compareTreeStructure(User $user)
    {
        $default_notifications_tree = NotificationsHelper::getNotificationsTree();
        $user_settings = $user->notifications_settings ?? [];

        $type_differences = array_diff_key($default_notifications_tree, $user_settings);
        if (!empty($type_differences)) {
            $user_settings = array_merge($user_settings, $type_differences);
        }

        $type_differences_with_default = array_diff_key($user_settings, $default_notifications_tree);
        if (!empty($type_differences_with_default)) {
            $user_settings = Arr::except($user_settings, array_keys($type_differences_with_default));
        }

        foreach ($default_notifications_tree as $type => $notifications) {
            // Find and add notifications which not contained in user settings field
            $differences[$type] = array_diff_key($notifications, $user_settings[$type]);
            if (!empty($differences[$type])) {
                $user_settings[$type] = array_merge($user_settings[$type], $differences[$type]);
            }

            // Find and remove notifications which not contained in default structure tree
            $differences_with_default[$type] = array_diff_key($user_settings[$type], $notifications);
            if (!empty($differences_with_default[$type])) {
                $user_settings[$type] = Arr::except($user_settings[$type], array_keys($differences_with_default[$type]));
            }
        }

        return $user_settings;
    }

    /**
     * Return array of available notifications
     *
     * @return array
     */
    public static function getNotifications()
    {
        return [
            'media' => [
                'created' => [
                   'notification' => MediaCreated::class,
                    'listener' => SendMediaCreatedNotification::class
                ],
                'banned' => [
                    'notification' => MediaBanned::class,
                    'listener' => SendMediaBannedNotification::class
                ],
                'verification' => [
                    'notification' => MediaVerification::class,
                    'listener' => SendMediaVerificationNotification::class
                ]
            ],
            'mediaPresent' => [
                'created' => [
                    'notification' => MediaPresentCreated::class,
                    'listener' => SendMediaPresentCreatedNotification::class
                ]
            ],
            'auction' => [
                'created' => [
                    'notification' => AuctionCreated::class,
                    'listener' => SendAuctionCreatedNotification::class
                ],
                'end_soon' => [
                    'notification' => AuctionEndSoon::class,
                    'listener' => SendAuctionEndSoonNotification::class
                ],
                'cancelled' => [
                    'notification' => AuctionCanceled::class,
                    'listener' => SendAuctionCancelledNotification::class
                ],
                'winner_banned' => [
                    'notification' => WinnerBanned::class,
                    'listener' => SendAuctionWinnerBannedNotification::class
                ],
            ],
            'auctionBid' => [
                'created' => [
                    'notification' => AuctionBidCreated::class,
                    'listener' => SendAuctionBidCreatedNotification::class,
                ],
                'outdated' => [
                    'notification' => AuctionBidOutdated::class,
                    'listener' => SendAuctionBidOutdatedNotification::class
                ]
            ],
            'advert' => [
                'created' => [
                    'notification' => AdvertCreated::class,
                    'listener' => SendAdvertCreatedNotification::class
                ],
                'cancelled' => [
                    'notification' => AdvertCancelled::class,
                    'listener' => SendAdvertCancelledNotification::class
                ],
                'updated' => [
                    'notification' => AdvertUpdated::class,
                    'listener' => SendAdvertUpdatedNotification::class
                ]
            ],
            'advertRespond' => [
                'created' => [
                    'notification' => AdvertRespondCreated::class,
                    'listener' => SendAdvertRespondCreatedNotification::class
                ]
            ],
            'meeting' => [
                'created' => [
                    'notification' => MeetingCreated::class,
                    'listener' => SendMeetingCreatedNotification::class
                ],
                'created_for_advert' => [
                    'notification' => AdvertMeetingCreated::class,
                    'listener' => SendAdvertMeetingCreatedNotification::class
                ],
                'created_for_auction' => [
                    'notification' => AuctionMeetingCreated::class,
                    'listener' => SendAuctionMeetingCreatedNotification::class
                ],
                'start_soon' => [
                    'notification' => MeetingStartSoon::class,
                    'listener' => SendMeetingStartSoonNotification::class
                ],
                'accepted' => [
                    'notification' => MeetingStatusChanged::class,
                    'listener' => SendMeetingStatusChangedNotification::class
                ],
                'cancelled' => [
                    'notification' => MeetingStatusChanged::class,
                    'listener' => SendMeetingStatusChangedNotification::class
                ],
                'failed' => [
                    'notification' => MeetingStatusChanged::class,
                    'listener' => SendMeetingStatusChangedNotification::class
                ],
                'confirmed' => [
                    'notification' => MeetingStatusChanged::class,
                    'listener' => SendMeetingStatusChangedNotification::class
                ],
                'confirmation_code' => [
                    'notification' => MeetingConfirmationCode::class,
                    'listener' => SendMeetingConfirmationCodeNotification::class
                ],
                'rate_needed' => [
                    'notification' => MeetingRateNeeded::class,
                    'listener' => SendMeetingRateNeededNotification::class
                ]
            ],
            'wantWithYou' => [
                'created' => [
                    'notification' => WantWithYouCreated::class,
                    'listener' => SendWantWithYouCreatedNotification::class
                ]
            ],
            'subscribe' => [
                'created' => [
                    'notification' => SubscribeCreated::class,
                    'listener' => SendSubscribeCreatedNotification::class
                ],
            ],
            'paymentOrder' => [
                'in' => [
                    'notification' => PaymentOrderIn::class,
                    'listener' => SendPaymentOrderNotification::class
                ],
                'out' => [
                    'notification' => PaymentOrderOut::class,
                    'listener' => SendPaymentOrderNotification::class
                ]
            ],
            'user' => [
                'reports_count_limit_reached' => [
                    'notification' => ReportsCountLimitReached::class,
                    'listener' => SendReportsCountLimitReachedNotification::class
                ]
            ],
            'adminPaymentTransaction' => [
                'created' => [
                    'notification' => AdminPaymentTransactionCreated::class,
                    'listener' => SendAdminPaymentTransactionCreatedNotification::class
                ],
            ],
            'usersPrivateChatRoomMessage' => [
                'created' => [
                    'notification' => MessageCreated::class,
                    'listener' => SendMessageCreatedNotification::class,
                ],
            ],
        ];
    }

    /**
     * Send push notification by device token
     *
     * @param User|Collection $notifiable
     * @param array $data
     * @param bool $highPriority
     */
    public static function pushNotification($notifiable, array $data, bool $highPriority = false) : void
    {
        if ($notifiable instanceof User) {
            $tokens = $notifiable
                ->devices
                ->pluck('device_token')
                ->unique()
                ->toArray();
        } elseif ($notifiable instanceof Collection) {
            $notifiable->load('devices');

            $tokens = $notifiable->pluck('devices')->map(function ($device) {
                return $device->pluck('device_token');
            })
                ->flatten()
                ->unique()
                ->toArray();
        } else {
            $tokens = [];
        }

        if (!empty($tokens)) {
            $notificationDriver = new PushNotification('fcm');

            if ($highPriority) {
                $notificationDriver->setConfig([
                    'priority' => 'high',
                ]);
            }

            $notificationDriver->setMessage([
                'data' => $data
            ])
                ->setDevicesToken($tokens)
                ->send();
        }
    }
}
