<?php


namespace App\GraphQL\ResolversOld\Auction;


use App\Events\Auction\AuctionBidNew;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Notifications\Auction\AuctionBidOutdated;
use App\Traits\RequestDataValidate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Illuminate\Support\Facades\Auth;

class AuctionBidResolver
{
    use RequestDataValidate;

    /**
     * @var Auction
     */
    protected $user;

    /**
     * Create auction bid
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $auction_id = $this->validatedData(
                $args['data'],
                ['auction_id' => 'required|integer']
            )['auction_id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $auction = Auction::whereId($auction_id)->firstOrFail();

        try {
            $min_value = ($auction->latest_bid ?? $auction->input_bid) + $auction->minimal_step;
            $value = $this->validatedData(
                $args['data'],
                ['value' => 'required|integer|min:' . $min_value .'|max:4294967200']
            )['value'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        //TODO add validation  for user credit balance

        $auction_bid = new AuctionBid();
        $auction_bid->user_id = $user->id;
        $auction_bid->auction_id = $auction->id;
        $auction_bid->value = $value;

        if (!$auction_bid->save()) {
            throw new GraphQLSaveDataException(__('auction_bid.create_failed'), __('Error'));
        }

        $auction_bids = AuctionBid::where('auction_id', $auction->id)->get();

        if($auction_bids->where('user_id', $user->id)->count() < 2) {
            $auction->increment('participants');
        }

        /** Send notification about auction bid outdated **/
        event(new AuctionBidNew($auction, $auction_bids, $user));

        return [
            'auction' => $auction,
            'bid' => $auction_bid
        ];
    }
}
