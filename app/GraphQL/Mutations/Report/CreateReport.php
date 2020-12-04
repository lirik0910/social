<?php

namespace App\GraphQL\Mutations\Report;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\Meeting;
use App\Models\Report;
use App\Models\User;
use App\Models\UsersPrivateChatRoom;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateReport
{
    use RequestDataValidate;

    /**
     * Create report
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLLogicRestrictException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     * @throws \ReflectionException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $author = $context->user();

        $current_reports_count = Report::where('author_id', $author->id)->whereRaw("date(created_at) = curdate()")->count();

        if($current_reports_count  >= 3) {
            throw new GraphQLLogicRestrictException(__('report.max_count_per_day'), __('Error'));
        }

        try {
            $inputs = $this->validatedData($args['data'], [
                'reported_type' => 'required|string|in:' . implode(',', array_keys(Report::availableParams('type'))),
                'reported_id' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $reported = Relation::getMorphedModel($inputs['reported_type'])::whereId($inputs['reported_id'])->firstOrFail();

        try {
            $inputs['reason'] = $this->validatedData($args['data'], [
                'reason' => 'required|integer|in:' . implode(',', array_keys(Report::availableParams('reason_' . $inputs['reported_type'])))
            ])['reason'];

        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $report = new Report();
        $report->author_id = $author->id;
        $report->reported_user_id = $this->getReportedUserId($reported);

        $report->fill($inputs);

        if (!$report->save()) {
            throw new GraphQLSaveDataException(__('report.create_failed'), __('Error'));
        }

        if($report->type === Report::TYPE_MEETINGS) {
            $report->reported()->update(['report_id' => $report->id]);
        }

        return $report;
    }

    /**
     * Return reported record`s user`s ID
     *
     * @param $reported
     * @return mixed
     */
    protected function getReportedUserId($reported)
    {
        switch (get_class($reported)) {
            case User::class:
                $user_id = $reported->id;
                break;
            case UsersPrivateChatRoom::class:
            case Meeting::class:
                $user_id = $reported->user_id === Auth::user()->id
                    ? $reported->seller_id
                    : $reported->user_id;
                break;
            default:
                $user_id = $reported->user_id;
                break;
        }

        return $user_id;
    }
}
