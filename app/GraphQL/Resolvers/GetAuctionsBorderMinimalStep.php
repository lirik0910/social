<?php


namespace App\GraphQL\Resolvers;


use App\Models\Auction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetAuctionsBorderMinimalStep
{
    /**
     * Get border values for auctions minimal steps filtering params
     *
     * @return mixed
     */
    public function resolve()
    {
        $user = Auth::user();

        $max_value = Auction
            ::where('user_id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('cancelled_at');
                $query->where('end_at', '>', Carbon::now());
            })
            ->orderBy('minimal_step', 'DESC')
            ->limit(1)
            ->select(['minimal_step as value']);

        $values = Auction
            ::where('user_id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('cancelled_at');
                $query->where('end_at', '>', Carbon::now());
            })
            ->limit(1)
            ->orderBy('minimal_step', 'ASC')
            ->union($max_value)
            ->get(['minimal_step as value'])
            ->toArray();

        if(count($values) < 1) {
            $values[0]['value'] = 0;
            $values[1]['value'] = 0;
        }

        return [
            'min' => $values[0]['value'],
            'max' => $values[1]['value'] ?? $values[0]['value']
        ];
    }
}
