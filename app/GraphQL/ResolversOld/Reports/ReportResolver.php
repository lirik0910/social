<?php


namespace App\GraphQL\ResolversOld\Reports;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Report;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class ReportResolver
{
    use RequestDataValidate;

    /**
     * Create report
     *
     * @param $rootValue
     * @param array $args
     *
     * @return Report
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \ReflectionException
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolveCreate($rootValue, array $args)
    {
        $author = Auth::user();

        if(Report::where('author_id', $author->id)->whereRaw("date(created_at) = curdate()")->count() >= 3) {
            throw new GraphQLLogicRestrictException(__('report.max_count_per_day'), __('Error'));
        }

        try {
            $type = $this->validatedData($args['data'], [
                'reported_type' => 'required|string|in:' . implode(',', array_keys(Report::availableParams('type')))
            ])['reported_type'];
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        try {
            $inputs = $this->validatedData($args['data'], [
                'reported_id' => 'required|integer|exists:' . $type . ',id',
                'reason' => 'required|integer|in:' . implode(',', array_keys(Report::availableParams('reason_' . $type)))
            ]);
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $report = new Report();
        $report->author_id = $author->id;
        $report->reported_type = $type;

        $report->fill($inputs);

        if (!$report->save()) {
            throw new GraphQLSaveDataException(__('report.create_failed'), __('Error'));
        }

        return $report;
    }
}


