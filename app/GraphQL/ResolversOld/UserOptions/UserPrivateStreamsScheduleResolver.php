<?php


namespace App\GraphQL\ResolversOld\UserOptions;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\UserPrivateStreamsOption;
use App\Models\UserPrivateStreamsSchedule;
use App\Traits\RequestDataValidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class UserPrivateStreamsScheduleResolver
{
    use RequestDataValidate;

    /**
     * @var UserPrivateStreamsOption
     */
    protected $options;

    /**
     * Create UserPrivateStreamsSchedule record
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return UserPrivateStreamsSchedule
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $this->options = Auth::user()->private_streams_options;

        $this->checkIntersect($inputs);

        $schedule_period = new UserPrivateStreamsSchedule();
        $schedule_period->private_streams_option_id = $this->options->id;

        $schedule_period->fill($inputs);

        if (!$schedule_period->save()) {
            throw new GraphQLSaveDataException(__('user_private_streams_option.update_failed'), __('Error'));
        }

        $weekday_periods = UserPrivateStreamsSchedule::where(['private_streams_option_id' => $this->options->id, 'weekday' => $inputs['weekday']])->orderBy('period_from', 'ASC')->get();

        return $weekday_periods;
    }

    /**
     * Update UserPrivateStreamsSchedule record
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return UserPrivateStreamsSchedule
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveUpdate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $this->options = Auth::user()->private_streams_options;

        $this->checkIntersect($inputs);

        $schedule_period = UserPrivateStreamsSchedule::where(['id' => $inputs['id'], 'private_streams_option_id' => $this->options->id])->first();

        $schedule_period->fill($inputs);

        if (!$schedule_period->save()) {
            throw new GraphQLSaveDataException(__('user_private_streams_option.update_failed'), __('Error'));
        }

        $weekday_periods = UserPrivateStreamsSchedule::where(['private_streams_option_id' => $this->options->id, 'weekday' => $inputs['weekday']])->orderBy('period_from', 'ASC')->get();

        return $weekday_periods;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id' => 'sometimes|integer|exists:user_private_streams_schedules',
            'weekday' => 'required|integer|min:1|max:7',
            'period_from' => 'required|date|before:period_to',
            'period_to' => 'required|date|after:period_from',
        ];

        return $rules;
    }

    /**
     * Check if exist schedule periods which intersect with input period
     *
     * @param array $inputs
     * @return void
     * @throws
     */
    public function checkIntersect(array $inputs)
    {
        $current_id = $inputs['id'] ?? 0;

        $schedules = UserPrivateStreamsSchedule::where(['private_streams_option_id' => $this->options->id, 'weekday' => $inputs['weekday']])
            ->where('id', '!=', $current_id)
            ->where(function ($query) use ($inputs) {
                $query->orWhere(function ($q) use ($inputs) {
                    $q->where('period_to', '>=', $inputs['period_to']);
                    $q->where('period_from', '<=', $inputs['period_to']);
                });

                $query->orWhere(function ($q) use ($inputs) {
                    $q->where('period_to', '>=', $inputs['period_from']);
                    $q->where('period_from', '<=', $inputs['period_from']);
                });

                $query->orWhere(function ($q) use ($inputs) {
                    $q->where('period_to', '>=', $inputs['period_from']);
                    $q->where('period_to', '<=', $inputs['period_to']);
                });
            })
            ->get();

        if($schedules->isNotEmpty()){
            throw new GraphQLLogicRestrictException(__('user_private_streams_option.cannot_intersect'), __('Error'));
        }

        return;
    }
}
