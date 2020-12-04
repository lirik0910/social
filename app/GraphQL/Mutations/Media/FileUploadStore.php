<?php

namespace App\GraphQL\Mutations\Media;

use App\Events\Media\MediaCreated;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Http\Requests\Media\FileUploadStoreRequest;
use App\Models\Media;
use App\Helpers\MediaHelper;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class FileUploadStore
{
    use DynamicValidation;

    /**
     * @param $rootValue
     * @param FileUploadStoreRequest $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, FileUploadStoreRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $inputs = $args->validated();

        $user = $context->user();

        $s3path = MediaHelper::getS3Path($inputs['type']);

        if (count($inputs['files']) < 1) {
            throw new GraphQLSaveDataException(__('media.no_files_were_sent'), __('Error'));
        }

        $media = [];
        foreach ($inputs['files'] as $file) {
            if (!MediaHelper::checkExists($s3path . '/' . $user->id . '/' . $file['name'])) {
                throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
            }

            $media[] = new Media([
                'type' => $inputs['type'],
                'name' => $file['name'],
                'mimetype' => $file['mimetype'],
                'size' => $file['size'],
                'description' => (array_key_exists('description', $file)) ? $file['description'] : "",
            ]);
        }

        $saved = $user->media()->saveMany($media);
        $saved_count = count($saved);

        if ($saved_count > 0) {
            event(new MediaCreated($saved[0], $saved_count));
        }

        return $media;
    }
}
