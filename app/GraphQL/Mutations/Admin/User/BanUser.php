<?php

namespace App\GraphQL\Mutations\Admin\User;

use App\Events\ActiveChatEvent;
use App\Events\User\BalanceChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\ChatRoomHelper;
use App\Helpers\EventHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\PaymentTransactionHelper;
use App\Http\Requests\Admin\User\BanUserRequest;
use App\Models\Advert;
use App\Models\AdvertRespond;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Meeting;
use App\Models\PaymentTransaction;
use App\Models\Report;
use App\Models\User;
use App\Models\UserBan;
use App\Models\UsersPrivateChatRoom;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BanUser
{
    use DynamicValidation;

    /**
     * Banned user
     *
     * @var User
     */
    protected $banned_user;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  BanUserRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, BanUserRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('user_ban', $user);

        $inputs = $args->validated();

        $id = Arr::get($inputs, 'id');

        $this->banned_user = User
            ::whereId($id)
            ->firstOrFail();

        if (!empty($this->banned_user->ban_id) || $this->banned_user->hasFlag(User::FLAG_USER_BANNED)) {
            throw new GraphQLLogicRestrictException(__('user.already_banned'), __('Error'));
        }

        $ban_record = new UserBan();
        $ban_record->user_id = $this->banned_user->id;
        $ban_record->reason = Arr::get($inputs, 'reason') ?? Arr::get($inputs, 'other_reason');
        $ban_record->unbanned_at = Arr::get($inputs, 'unbanned_date') ?? null;

        if (!$ban_record->save()) {
            throw new GraphQLSaveDataException(__('user.ban_record_create_failed'), __('Error!'));
        }

        $this->banned_user->ban_id = $ban_record->id;
        $this->banned_user->addFlag(User::FLAG_USER_BANNED);

        if (!$this->banned_user->save()) {
            throw new GraphQLSaveDataException(__('user.update_failed'), __('Error!'));
        }

        $this->cancelUserActivities();
        $this->updateTransactions();
        $this->endChatRooms();

        return [
            'user' => $this->banned_user,
            'ban_record' => $ban_record
        ];
    }

    /**
     * Cancel current activities for banned user
     */
    protected function cancelUserActivities()
    {
        Meeting
            ::whereIn('status', [Meeting::STATUS_NEW, Meeting::STATUS_ACCEPTED])
            ->where(function ($query) {
                $query
                    ->where('user_id', $this->banned_user->id)
                    ->orWhere('seller_id', $this->banned_user->id);
            })
            ->update([
                'status' => Meeting::STATUS_DECLINED,
            ]);

        Advert
            ::active()
            ->where('user_id', $this->banned_user->id)
            ->update([
                'cancelled_at' => DB::raw('NOW()')
            ]);

        Auction
            ::active()
            ->where('user_id', $this->banned_user->id)
            ->update([
                'cancelled_at' => DB::raw('NOW()')
            ]);

        AdvertRespond
            ::where('user_id', $this->banned_user->id)
            ->whereHas('advert', function ($query) {
                $query->active();
            })
            ->delete();
    }

    /**
     * Update transactions for banned user
     */
    protected function updateTransactions()
    {
        $transactions = PaymentTransaction
            ::where('status', PaymentTransaction::TRANSACTION_STATUS_FREEZED)
            ->where(function ($query) {
                $query
                    ->where('from_user_id', $this->banned_user->id)
                    ->orWhere('to_user_id', $this->banned_user->id);
            })
            ->get();

        if (!empty($transactions)) {
            $transactions_ids = $transactions->modelKeys();

            $updating_result = PaymentTransaction
                ::whereIn('id', $transactions_ids)
                ->update([
                    'status' => PaymentTransaction::TRANSACTION_STATUS_CANCELLED
                ]);

            if ($updating_result) {
                $changed_users = [];

                foreach ($transactions->fresh() as $transaction) {
                    $changed_users[] = PaymentTransactionHelper::changeUserBalance($transaction);
                    $this->sendNotifications($transaction);
                }

                $changed_users_ids = collect($changed_users)->flatten()->pluck('id')->unique()->all();

                foreach ($changed_users_ids as $changed_users_id) {
                    event(new BalanceChanged($changed_users_id));
                }
            }
        }
    }

    /**
     * Send notifications for cancelled activities
     *
     * @param $transaction
     */
    protected function sendNotifications($transaction)
    {
        $cancelled_model = Relation::getMorphedModel($transaction->source_type);

        if (in_array($cancelled_model, [Auction::class, Advert::class, Meeting::class])) {
            $cancelled = $cancelled_model
                ::whereId($transaction->source_id)
                ->first();

            if (!empty($cancelled)) {
                $formatted_model_name = EventHelper::getFormattedModelName($cancelled_model);

                NotificationsHelper::handle(['cancelled'], $cancelled, $formatted_model_name);
            }
        }
    }

    /**
     * End chat rooms for banned user
     */
    protected function endChatRooms()
    {
        $rooms = UsersPrivateChatRoom
            ::where(function ($query) {
                $query
                    ->where('user_id', $this->banned_user->id)
                    ->orWhere('seller_id', $this->banned_user->id);
            })
            ->whereNull('ended_at')
            ->get();

        $rooms_ids = $rooms
            ->pluck('id')
            ->toArray();

        UsersPrivateChatRoom
            ::whereIn('id', $rooms_ids)
            ->update([
                'ended_at' => Carbon::now(),
            ]);

        foreach ($rooms as $room) {
            $room->refresh();

            $data = ChatRoomHelper::formatData(ChatRoomHelper::CHAT_ROOM_EVENT_ENDED, $this->banned_user, $room);

            $receiver_id = $this->banned_user->id === $room->user_id
                ? $room->seller_id
                : $room->user_id;

            event(new ActiveChatEvent($receiver_id, $data));
        }
    }
}
