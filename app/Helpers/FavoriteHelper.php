<?php

namespace App\Helpers;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Subscribe;
use App\Models\SubscriberUserPublications;
use App\Events\FavoriteCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class FavoriteHelper
{
    /**
     * @param $user
     * @param $type
     * @param $object
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public static function createPublication($type, $object)
    {
        $publication = new SubscriberUserPublications();
        $publication->owner_id = $object->user_id;
        $publication->pub_type = $type;
        $publication->pub_id = $object->id;

        if(!$publication->save()){
            throw new GraphQLSaveDataException(__('chat.create_message_failed'), __('Error'));
        }

        $eventsData = self::getEventsData($type, $object, $publication->id);

        event(new FavoriteCreated($eventsData));
    }

    /**
     * @param $type
     * @param $object
     * @param $id
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getEventsData($type, $object, $id)
    {
        $subscribers = User
            ::leftJoin('subscribes', function ($join) {
                $join->on('subscribes.subscriber_id', '=', 'users.id');
            })
            ->where('subscribes.user_id', '=', $object->user_id)
            ->get(['users.*']);

        return [
            'subscribers' => $subscribers,
            'owner' => self::getPubUserFormattedData($object),
            'pub' => self::getPubFormattedData($object, $type),
            'pub_type' => strtoupper($type),
            'id' => (string) $id
        ];
    }

    /**
     * Return publication formatted data
     *
     * @param $object
     * @param $type
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getPubFormattedData($object, $type)
    {
        switch ($type) {
            case SubscriberUserPublications::PUB_TYPE_AUCTIONS:
                $data = [
                    'id' => (string) $object->id,
                    'input_bid' => $object->input_bid,
                    'latest_bid' => $object->latest_bid,
                    'description' => $object->description,
                    'created_at' => $object->created_at,
                    'cancelled_at' => $object->cancelled_at,
                    'end_at_datetime' => $object->end_at
                ];
                break;
            case SubscriberUserPublications::PUB_TYPE_ADVERTS:
                $data = [
                    'id' => (string) $object->id,
                    'type' => $object->type,
                    'address' => $object->address,
                    'min_age' => $object->min_age,
                    'max_age' => $object->max_age,
                    'location_lat' => $object->location_lat,
                    'location_lng' => $object->location_lng,
                    'meeting_date' => $object->meeting_date,
                    'price' => $object->price,
                    'photo_verified_only' => $object->photo_verified_only,
                    'safe_deal_only' => $object->safe_deal_only,
                    'outfit' => $object->outfit,
                    'respond_id' => $object->respond_id,
                    'created_at' => $object->created_at,
                    'cancelled_at' => $object->cancelled_at,
                    'charity_organization_id' => $object->charity_organizaion_id,
                    'end_at_datetime' => $object->end_at
                ];
                break;
            case SubscriberUserPublications::PUB_TYPE_MEDIA:
                $thumb_sizes = MediaHelper::getThumbSizes($object);

                $data = [
                    'id' => (string) $object->id,
                    'type' => $object->type,
                    'description' => $object->description,
                    'views' => $object->views,
                    'media_uri' => $object->media_uri,
                    'created_at' => $object->created_at,
                    'thumbs' => $object->getThumbs($object, ['sizes' => $thumb_sizes])
                ];
                break;
            default:
                throw new GraphQLLogicRestrictException(__('common.publication_type_missing'), __('Error!'));
        }

        return $data;
    }

    /**
     * Return publication`s user formatted data
     *
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getPubUserFormattedData($object)
    {
        $pub_user = Auth::user()->id === $object->user_id
            ? Auth::user()
            : $object->user;

        $thumbs_sizes = MediaHelper::getThumbSizes($pub_user);

        return [
            'id' => (string) $pub_user->id,
            'nickname' => $pub_user->nickname,
            'slug' => $pub_user->slug,
            'avatar' => $pub_user->avatar,
            'avatar_thumbs' => $pub_user->getThumbs($pub_user, ['sizes' => $thumbs_sizes]),
        ];
    }
}
