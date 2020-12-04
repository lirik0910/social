<?php

namespace App\GraphQL\Mutations\Admin\Report;

use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Report\DeclineReportRequest;
use App\Models\Report;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DeclineReport
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param DeclineReportRequest $args The arguments that were passed into the field.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     * @throws \ReflectionException
     */
    protected function resolve($rootValue, DeclineReportRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $reported_type = Arr::get($inputs, 'reported_type');
        $reported_id = Arr::get($inputs, 'reported_id');

        $user = $context->user();

        AdminPermissionsHelper::check(Report::getPermissionNameByType($reported_type), $user);

        $reports = Report
            ::where([
                'reported_type' => $reported_type,
                'reported_id' => $reported_id,
                'status' => Report::STATUS_PENDING,
            ])
            ->get();

        $reports_ids = $reports->modelKeys();

        $updated_count = DB::table('reports')
            ->whereIn('id', $reports_ids)
            ->update([
                'status' => Report::STATUS_DECLINED
            ]);

        return $reports->fresh();
    }
}
