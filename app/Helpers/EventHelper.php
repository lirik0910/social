<?php

namespace App\Helpers;

use App\Jobs\AuctionEndSoonNotification;
use App\Jobs\AuctionMeetingCreate;
use App\Jobs\MeetingConfirmationCode;
use App\Jobs\MeetingFailedStatusChange;
use App\Jobs\MeetingRateNeededNotificationSend;
use App\Jobs\MeetingRequestDelete;
use App\Libraries\GraphQL\User\MeetingsOptionsCreate;
use App\Libraries\GraphQL\User\NotificationsSettingsCreate;

class EventHelper
{
    const HELPER_CLASS_METHOD = 'resolve';

    const MODELS_ACTION = [
        'PaymentOrder' => [
            'in' => [
                'notifications' => [
                    'in'
                ]
            ],
            'out' => [
                'notifications' => [
                    'out'
                ]
            ],
        ],
        'MediaPresent' => [
            'created' => [
                'notifications' => [
                    'created'
                ],
                'transaction'
            ]
        ],
        'Media' => [
            'created' => [],
            'banned' => [
                'notifications' => [
                    'banned'
                ]
            ],
            'verification' => [
                'notifications' => [
                    'verification'
                ]
            ]
        ],
        'Meeting' => [
            'created' => [
                'notifications' => [
                    'created'
                ],
                'transaction',
                'jobs' => [
                    MeetingRequestDelete::class
                ]
            ],
            'created_for_advert' => [
                'notifications' => [
                    'created_for_advert',
                    'start_soon'
                ],
                'jobs' => [
                    MeetingConfirmationCode::class,
                    MeetingFailedStatusChange::class
                ]
            ],
            'created_for_auction' => [
                'notifications' => [
                    'created_for_auction',
                    'start_soon'
                ],
                'jobs' => [
                    MeetingConfirmationCode::class,
                    MeetingFailedStatusChange::class
                ]
            ],
            'accepted' => [
                'notifications' => [
                    'accepted',
                    'start_soon'
                ],
                'jobs' => [
                    MeetingConfirmationCode::class,
                    MeetingFailedStatusChange::class,
                    MeetingRateNeededNotificationSend::class,
                ]
            ],
            'cancelled' => [
                'notifications' => [
                    'cancelled'
                ],
                'transaction',
            ],
            'failed' => [
                'notifications' => [
                    'failed'
                ],
                'transaction',
            ],
            'confirmed' => [
                'notifications' => [
                    'confirmed'
                ],
                'transaction',
            ],
        ],
        'Advert' => [
            'created' => [
                'notifications' => [
                    'created'
                ],
                'transaction',
            ],
            'cancelled' => [
                'notifications' => [
                    'cancelled'
                ],
                'transaction',
            ],
            'updated' => [
                'transaction',
                'notifications' => [
                    'updated'
                ],
            ]
        ],
        'AdvertRespond' => [
            'created' => [
                'notifications' => [
                    'created'
                ],
                'transaction',
            ],
            'deleted' => [
                'transaction',
            ],
        ],
        'Auction' => [
            'cancelled' => [
                'notifications' => [
                    'cancelled'
                ],
                'transaction'
            ],
            'created' => [
                'notifications' => [
                    'created'
                ],
                'jobs' => [
                    AuctionEndSoonNotification::class,
                    AuctionMeetingCreate::class
                ]
            ]
        ],
        'AuctionBid' => [
            'created' => [
                'notifications' => [
                    'created',
                    'outdated'
                ],
                'transaction'
            ]
        ],
        'WantWithYou' => [
            'created' => [
                'notifications' => [
                    'created'
                ]
            ]
        ],
        'Subscribe' => [
            'created' => [
                'notifications' => [
                    'created'
                ]
            ],
            'updated' => [
                'notifications' => [
                    'created'
                ]
            ]
        ],
        'Profile' => [
            'created' => [
                MeetingsOptionsCreate::class,
                NotificationsSettingsCreate::class
            ]
        ],
        'CharityOrganization' => [
            'deleted' => [
                'notifications' => [
                    'deleted'
                ]
            ]
        ],
        'User' => [
            'reports_count_updated' => [
                'notifications' => [
                    'reports_count_limit_reached'
                ]
            ]
        ],
        'AdminPaymentTransaction' => [
            'created' => [
                'notifications' => [
                    'created'
                ],
                'transaction'
            ]
        ],
    ];

    /**
     * Listening and handling events
     *
     * @param $event_name
     * @param $object
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public static function handle($event_name, $object)
    {
        $events_array = self::getEventsArray($event_name);
        $object = $object[0];

        $event_name = self::getCustomEventName($events_array['event_name'], $object);
        $model_name = str_replace(config('app.modelFolder'), "", $events_array['event_model']);

        $actions = self::MODELS_ACTION[$model_name][$event_name] ?? [];

        if(!empty($actions)) {
            foreach ($actions as $key => $action) {
                $key = is_array($action) ? $key : $action;
                switch ($key) {
                    case 'notifications':
                        NotificationsHelper::handle($action, $object, $model_name);
                        break;
                    case 'transaction':
                        PaymentTransactionHelper::paymentTransaction($event_name, $model_name, $object);
                        break;
                    case 'jobs':
                        foreach($action as $job) {
                            $job::dispatch($object);
                        }
                        break;
                    default:
                        if(class_exists($action)) {
                            call_user_func([new $action, self::HELPER_CLASS_METHOD], $object);
                        }
                        break;
                }
            }
        }

        return;
    }

    /**
     * Return custom event name for current model if exists
     *
     * @param $event_name
     * @param $object
     * @return null
     */
    protected static function getCustomEventName($event_name, $object)
    {
        if(method_exists($object, 'getCustomEventName')) {
            $event_name = $object->getCustomEventName($event_name);
        }

        return $event_name ?? null;
    }

    /**
     * @param $eventNameString
     * @return array
     */
    public static function getEventsArray($eventNameString)
    {
        $eventNameArray = explode(": ", $eventNameString);

        return ['event_name' => strtr($eventNameArray[0], ['eloquent.'=>'']), 'event_model' => $eventNameArray[1]];
    }

    /**
     * Return model name formatted for helpers structure
     *
     * @param string $model_name
     * @return string
     */
    public static function getFormattedModelName(string $model_name)
    {
        return lcfirst(str_replace(config('app.modelFolder'), "", $model_name));
    }
}
