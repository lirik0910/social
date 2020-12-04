<?php

namespace App\GraphQL\Mutations\Admin\VerificationSign;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Helpers\MediaHelper;
use App\Http\Requests\Admin\VerificationSign\UploadVerificationSignsRequest;
use App\Models\PhotoVerification;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UploadVerificationSigns
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  UploadVerificationSignsRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    protected function resolve($rootValue, UploadVerificationSignsRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('verification', $user);

        $files = Arr::get($args->validated(), 'files');

        $signs = [];

        foreach ($files as $file) {
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION_SIGN);

            $image_info = MediaHelper::uploadAdminImage($file, $s3path);

            $sign = Arr::only($image_info, ['name', 'mimetype', 'size']);
            $sign['created_at'] = Carbon::now();
            $sign['updated_at'] = Carbon::now();

            array_push($signs, $sign);
        }

        $signs_count = count($signs);

        if ($signs_count > 0 && !PhotoVerification::insert($signs)) {
            throw new GraphQLSaveDataException(__('photo_verification.save_failed'), __('Error!'));
        }

        return PhotoVerification
            ::limit($signs_count)
            ->orderByDesc('created_at')
            ->get();
    }
}
