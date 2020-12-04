<?php

namespace App\GraphQL\Mutations\Admin\Report;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Report\ApproveReportRequest;
use App\Http\Requests\General\IDRequiredRequest;
use App\Models\Advert;
use App\Models\Auction;
use App\Models\Media;
use App\Models\Report;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ApproveReport
{
    use DynamicValidation, RequestDataValidate;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  ApproveReportRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, ApproveReportRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $reported_type = Arr::get($inputs, 'reported_type');
        $reported_id = Arr::get($inputs, 'reported_id');

        $user = $context->user();

        AdminPermissionsHelper::check(Report::getPermissionNameByType($reported_type), $user);

        try {
            $moderation_reason = $this->validatedData($inputs, [
                'moderation_reason' => 'in:' . implode(',', array_keys(Report::availableParams('reason_' . $reported_type)))
            ])['moderation_reason'];
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $reports = Report
            ::where([
                'reported_type' => $reported_type,
                'reported_id' => $reported_id,
                'status' => Report::STATUS_PENDING,
            ])
            ->get();

        if ($reports->count() < 1) {
            throw new GraphQLLogicRestrictException(__('report.reports_not_found'), __('Error!'));
        }

        $reports_ids = $reports->modelKeys();

        $updated_count = DB::table('reports')
            ->whereIn('id', $reports_ids)
            ->update([
                'moderation_reason' => $moderation_reason,
                'status' => Report::STATUS_APPROVED
            ]);

        $this->updateReported($reported_type, $reported_id);
        $this->updateReportedUser($reports->first()->reported_user_id, $updated_count);

        return $reports->fresh();
    }

    /**
     * @param string $reported_type
     * @param string $reported_id
     * @throws GraphQLSaveDataException
     */
    protected function updateReported(string $reported_type, string $reported_id)
    {
        $reported_model = Relation::getMorphedModel($reported_type);

        switch ($reported_model) {
            case Advert::class:
            case Auction::class:
                $reported = $reported_model
                    ::active()
                    ->whereId($reported_id)
                    ->first();

                $data = [
                    'cancelled_at' => DB::raw('NOW()'),
                ];
                break;
            case Media::class:
                $reported = $reported_model
                    ::where('status', '!=', Media::STATUS_BANNED)
                    ->whereId($reported_id)
                    ->first();

                $data = [
                    'status' => Media::STATUS_BANNED,
                ];
                break;
            default:
                $data = [];
                break;
        }

        if (!empty($reported) && !empty($data)) {
            $reported->fill($data);

            if (!$reported->save()) {
                throw new GraphQLSaveDataException(__('report.reported_update_failed'), __('Error!'));
            }
        }
    }

    /**
     * @param $reported_user_id
     * @param $new_reports_count
     * @throws GraphQLSaveDataException
     */
    protected function updateReportedUser($reported_user_id, $new_reports_count)
    {
        $reported_user = User
            ::whereId($reported_user_id)
            ->firstOrFail();

        $reported_user->reports_count += $new_reports_count;

        if (!$reported_user->save()) {
            throw new GraphQLSaveDataException(__('user.update_failed'), __('Error!'));
        }
    }
}
