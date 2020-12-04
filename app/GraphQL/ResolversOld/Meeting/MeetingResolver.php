<?php


namespace App\GraphQL\ResolversOld\Meeting;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Meeting;
use App\Models\User;
use App\Models\UserMeetingsOption;
use App\Events\Meeting\MeetingRequested;
use App\Events\Meeting\MeetingAccepted;
use App\Events\Meeting\MeetingConfirmed;
use App\Events\Meeting\MeetingRejected;
use App\Events\Meeting\MeetingEdited;
use App\Events\Meeting\MeetingDeclined;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class MeetingResolver
{
    use RequestDataValidate;

    /**
     * Seller meeting options
     *
     * @var UserMeetingsOption
     */
    protected $seller_meeting_options;


    /**
     * Create meeting
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        $user = Auth::user();

        //TODO validation for needed count of user credits availability

        try {
            $seller_id = $this->validatedData($args['data'], [
                'seller_id' => 'required|integer'
            ])['seller_id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $seller = User::whereId($seller_id)->firstOrFail();
        $this->seller_meeting_options = $seller->meetings_options;

        $user_age = $user->profile->years;
        if($user_age < $this->seller_meeting_options->min_age || $user_age > $this->seller_meeting_options->max_age) {
            throw new GraphQLLogicRestrictException(__('meeting.age_restrict'), __('Error'));
        }

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = new Meeting();
        $meeting->user_id = $user->id;
        $meeting->seller_id = $seller_id;
        $meeting->status = Meeting::STATUS_NEW;
        $meeting->charity_organization_id = $this->seller_meeting_options->charity_organization_id;

        $meeting->fill($inputs);

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.create_failed'), __('Error'));
        }

/*        if($meeting->safe_deal) {
            //TODO operation with user credits
        }*/

        /** Send notification about received meeting request **/
        event(new MeetingRequested($meeting, $user, $seller));

        return $meeting;
    }

    /**
     * Edit/Update meeting by seller
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveEdit($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args['data'], [
                'id' => 'required|integer',
            ])['id'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = Meeting::whereId($id)->firstOrFail();

        if(Carbon::now()->diffInHours($meeting->updated_at) >= 24) {
            throw new GraphQLLogicRestrictException(__('meeting.is_expired'), __('Error'));
        }

        $user = Auth::user();

        if($meeting->seller_id === $user->id) {
            $this->seller_meeting_options = $user->meetings_options;
        } else {
            $seller = User::whereId($meeting->seller_id)->firstOrFail();
            $this->seller_meeting_options = $seller->meeting_options;
        }

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting->status = Meeting::STATUS_EDITED;

        //  Find and add to array edited fields
        $edited_fields = [];
        foreach($inputs as $key => $value) {
            if($meeting->$key != $value) {
                if($key === 'meeting_date') {
                    $edited_fields[$key] = $meeting->$key->format('c');
                } else {
                    $edited_fields[$key] = $meeting->$key;
                }
            }
        }

        $meeting->fill($inputs);

        // Override current object field
        if(!empty($edited_fields)) {
            $meeting->edited_fields = json_encode($edited_fields);
            $meeting->edited_by = $user->id;
        }

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        /** Send notification about meeting edited **/
        event(new MeetingEdited($meeting, $seller));

        return $meeting;
    }

    /**
     * Change meeting status to Accepted
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveAccept($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = Meeting::whereId($id)->firstOrFail();

        if(Carbon::now() > $meeting->meeting_date || Carbon::now()->diffInHours($meeting->updated_at) >= 24) {
            throw new GraphQLLogicRestrictException(__('meeting.is_expired'), __('Error'));
        }

        $meeting->status = Meeting::STATUS_ACCEPTED;

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        /** Send meeting accepted and start soon notification **/
        event(new MeetingAccepted($meeting));

        return $meeting;
    }

    /**
     * Change meeting status to Rejected
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveReject($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = Meeting::whereId($id)->firstOrFail();

        $meeting->status = Meeting::STATUS_REJECTED;

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        /** Send meeting rejected notification **/
        event(new MeetingRejected($meeting));

        return $meeting;
    }

    /**
     * Decline the planned meeting
     *
     * @param $rootValue
     * @param array $args
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveDecline($rootValue, array $args)
    {
        try {
            $id = $this->validatedData($args, [
                'id' => 'required|integer'
            ])['id'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = Meeting::whereId($id)->firstOrFail();

        if($meeting->status !== Meeting::STATUS_ACCEPTED) {
            throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error'));
        }

        $meeting->status = Meeting::STATUS_DECLINED;

        if(!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        /** Send notification about declined meeting **/
        event(new MeetingDeclined($meeting));

        return $meeting;
    }

    /**
     * Generate and send confirmation code
     *
     * @param $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws GraphQLSaveDataException
     */
    public function resolveSendCode($rootValue, array $args)
    {
        $meeting = Meeting::whereId($args['id'])->first();

        $confirmation_code = rand(1000, 9999);
        $meeting->confirmation_code = Hash::make($confirmation_code);

        if (!$meeting->save()) {
            throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
        }

        //TODO confirmation code sending logic

        return $meeting;
    }

    /**
     * Confirm meeting
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return Meeting $meeting
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveConfirm($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data'], [
                'id' => 'required|integer',
                'confirmation_code' => 'required|string|size:4'
            ]);
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $meeting = Meeting::whereId($inputs['id'])->firstOrFail();

        if($meeting->status !== Meeting::STATUS_ACCEPTED) {
            throw new GraphQLLogicRestrictException(__('meeting.incorrect_status'), __('Error'));
        }

        if(Hash::check($inputs['confirmation_code'], $meeting->confirmation_code)) {
            $meeting->status = Meeting::STATUS_CONFIRMED;

            if (!$meeting->save()) {
                throw new GraphQLSaveDataException(__('meeting.update_failed'), __('Error'));
            }

            /** Send notification to seller about meeting confirmed */
            event(new MeetingConfirmed($meeting));

            return $meeting;

        } else {
            throw new GraphQLValidationException(__('meeting.code_validation_failed'), __('Error'));
        }
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
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'meeting_date' => 'required|date|after:' . Carbon::now()->addMinutes(30),
            'price' => 'required|integer|min:' . $this->seller_meeting_options->minimal_price . '|max:4294967200',
            'outfit' => 'required|integer|in:' . implode(',', array_keys(Meeting::availableParams('outfit'))),
            'safe_deal' => 'required|boolean',
            'edit_note' => 'nullable|string',
        ];

        if($this->seller_meeting_options->safe_deal_only) {
            $rules['safe_deal'] .= '|accepted';
        }

        return $rules;
    }
}
