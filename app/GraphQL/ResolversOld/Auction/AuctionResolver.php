<?php


namespace App\GraphQL\ResolversOld\Auction;


use App\Events\Auction\AuctionCanceled;
use App\Events\Auction\AuctionEnded;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Auction;
use App\Events\Auction\AuctionStarted;
use App\Models\Meeting;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class AuctionResolver
{
    use RequestDataValidate;

    /**
     * Create auction
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Auction
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws  GraphQLLogicRestrictException
     * @throws  GraphQLSaveDataException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = Auth::user();

        if(!$user->image) {
            throw new GraphQLLogicRestrictException(__('auction.avatar_not_exist'), __('Error'));
        }

        $auction = new Auction();
        $auction->user_id = $user->id;

        $auction->fill($inputs);

        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        /** Send notification about auction started **/
        event(new AuctionStarted($auction, $user));

        return $auction;
    }

    /**
     * Update auction
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws  GraphQLLogicRestrictException
     * @throws  GraphQLSaveDataException
     */
    public function resolveUpdate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $auction = Auction::whereId($inputs['id'])->firstOrFail();

        if($auction->bids->isNotEmpty()) {
            throw new GraphQLLogicRestrictException(__('auction.cannot_update'), __('Error'));
        }

        $auction->fill($inputs);

        if (!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        return $auction;
    }

    /**
     * Cancel auction
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCancel($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $auction = Auction::whereId($id)->firstOrFail();

        if($auction->ended_at) {
            throw new GraphQLLogicRestrictException(__('auction.already_ended'), __('Error'));
        }

        $auction->ended_at = Carbon::now();

        if(!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        //TODO Return credits to user who are made the last bid

        event(new AuctionCanceled($auction));

        return $auction;
    }

    /**
     * End auction
     *
     * @param $rootValue
     * @param array $args
     * @return Auction
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveEnd($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $auction = Auction::whereId($id)->firstOrFail();

        if($auction->ended_at) {
            throw new GraphQLLogicRestrictException(__('auction.already_ended'), __('Error'));
        }

        $auction->ended_at = Carbon::now();

        if(!$auction->save()) {
            throw new GraphQLSaveDataException(__('auction.update_failed'), __('Error'));
        }

        event(new AuctionEnded($auction));

        return $auction;
    }

    /**
     * List of validation rules
     *
     * @return array
     * @throws
     */
    public function rules()
    {
        $rules = [
            'id' => 'sometimes|required|integer',
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => 'required|date|after:end_at',
            'input_bid' => 'required|integer|min:1|max:4294967200',
            'minimal_step' => 'required|integer|min:1|max:4294967200',
            'min_age' => 'required|integer|min:0|max:99|lte:max_age',
            'max_age' => 'required|integer|min:0|max:99|gte:min_age',
            'description' => 'nullable|string|max:120',
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            'charity_organization_id' => 'nullable|integer|exists:charity_organizations,id',
            'photo_verified_only' => 'required|boolean',
            'fully_verified_only' => 'required|boolean',
            'location_for_winner_only' => 'required|boolean',
            'end_at' => 'required|date|before:meeting_date|after:' . Carbon::now() //TODO add validation for minimal auction duration
        ];

        return $rules;
    }

}
