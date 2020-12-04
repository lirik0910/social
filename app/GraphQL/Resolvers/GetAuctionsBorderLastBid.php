<?php


namespace App\GraphQL\Resolvers;


use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetAuctionsBorderLastBid
{

    public function resolve()
    {
        $user = Auth::user();

        $sub_query = Auction
            ::where('auctions.user_id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('cancelled_at');
                $query->where('end_at', '>', Carbon::now());
            })
            ->whereNull('last_bid_id')
            ->selectRaw('min(auctions.input_bid) as input_min, max(auctions.input_bid) as input_max');

        $values = Auction
            ::where('auctions.user_id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('cancelled_at');
                $query->where('end_at', '>', Carbon::now());
            })
            ->leftJoin('auction_bids', 'auctions.id', '=', 'auction_bids.auction_id')
            ->selectRaw('min(auction_bids.value) as min, max(auction_bids.value) as max')
            ->union($sub_query)
            ->get()
            ->toArray();

        $min_values = Arr::where(Arr::pluck($values, 'min'), function ($value, $key) {
            return is_integer($value);
        });

        $max_values = Arr::where(Arr::pluck($values, 'max'), function ($value, $key) {
            return is_integer($value);
        });

        return [
            'min' => count($min_values) ?  min($min_values) : 0,
            'max' => count($max_values) ? max($max_values) : 0
        ];
    }
}
