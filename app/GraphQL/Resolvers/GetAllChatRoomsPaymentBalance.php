<?php


namespace App\GraphQL\Resolvers;


use App\Models\UsersPrivateChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetAllChatRoomsPaymentBalance
{
    public function resolve()
    {
        $user = Auth::user();

        $sub_query = UsersPrivateChatRoom
            ::where('amount', '>', 0)
            ->where('seller_id', $user->id)
            ->select(DB::raw('SUM(amount) as earned'));

        $balance = UsersPrivateChatRoom
            ::where('amount', '>', 0)
            ->where('user_id', $user->id)
            ->select(DB::raw('SUM(amount) as spent'))
            ->addSelect(['earned' => $sub_query])
            ->first()
            ->only(['earned', 'spent']);

        return array_map(function ($item) {
            return is_null($item)
                ? 0
                : $item;
            }, $balance
        );
    }
}
