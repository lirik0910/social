<?php

namespace App\GraphQl\ResolversOld\Upload;

use App\Helpers\MediaHelper;
use App\Models\Media;
use App\Models\Report;
use App\Models\User;
use App\Models\MediaUsersView;
use App\Traits\RequestDataValidate;
use App\Exceptions\GraphQLSaveDataException;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class Upload
{
    use RequestDataValidate;

    const MEDIA_SIZE = 904857600;

//    /**
//     * View all users media with paginations
//     *
//     * @param $rootValue
//     * @param array $args
//     *
//     * @return array
//     * @throws \App\Exceptions\GraphQLSaveDataException
//     */
//    public function resolveUsersMedia($rootValue, array $args)
//    {
//        $medias = Media::where('user_id', \Auth::user()->id)->where('type', '!==', Media::TYPE_AVATAR)->paginate(5);
//
//        $results = [];
//        foreach ($medias as $media) {
//            array_push($results, [
//                'id' => $media->id,
//                'name' => $media->name,
//                'mimetype' => $media->mimetype,
//                'size' => $media->size,
//                'description' => $media->description,
//                'views' => $media->views,
//                'presents_cost' => $media->presents_cost,
//                'media_uri' => $media->media_uri,
//            ]);
//        }
//
//        return ['medias' => $results];
//    }

    /**
     * Generate presigned urls for each files
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function resolveFileUploadGenerate($rootValue, array $args)
    {
        $inputs = $args['data'];
        $s3path = MediaHelper::getS3Path($inputs['type']);

        if (count($inputs['files']) > 0) {
            $results = [];
            foreach ($inputs['files'] as $input) {
                try {
                    $input = $this->validatedData($input);
                } catch (ValidationException $e) {
                    throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
                }
                $presignedMediaUrl = MediaHelper::createPresignedUrl($inputs['type'], $input, $s3path . '/' . \Auth::user()->id);
                $presignedThumbnailUrl = MediaHelper::createThumbnailPresignedUrl($inputs['type'], $input, $s3path . '/' . \Auth::user()->id, $presignedMediaUrl['name']);
                $results[$input['id']] = [
                    'name' => $presignedMediaUrl['name'],
                    'uri' => $presignedMediaUrl['uri'],
                    'mimetype' => $input['mimetype'],
                    'size' => $input['size'],
                    'thumbs' => $presignedThumbnailUrl,
                ];
            }
            return [
                'status' => 'URL(s) successfully generated',
                'results' => $results,
            ];
        } else {
            throw new GraphQLSaveDataException(__('media.no_files_were_sent'), __('Error'));
        }
    }

    /**
     * Store uploaded media in to DB
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function resolveFileUploadStore($rootValue, array $args)
    {
        $user = \Auth::user();
        $inputs = $args['data'];
        $s3path = MediaHelper::getS3Path($inputs['type']);
        if (count($inputs['files']) > 0) {
            $medias = [];
            foreach ($inputs['files'] as $file) {
                try {
                    $file = $this->validatedData($file);
                } catch (ValidationException $e) {
                    throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
                }
                if (!MediaHelper::checkExists($s3path . '/' . $user->id . '/' . $file['name']))
                    throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
                $media = new Media([
                    'type' => $inputs['type'],
                    'name' => $file['name'],
                    'mimetype' => $file['mimetype'],
                    'size' => $file['size'],
                    'description' => (array_key_exists('description', $file)) ? $file['description'] : "",
                ]);
                $medias[] = $media;
            }
            $user->media()->saveMany($medias);
            return [
                'status' => 'File(s) saved successfully',
                'data' => $medias
            ];
        } else {
            throw new GraphQLSaveDataException(__('media.no_files_were_sent'), __('Error'));
        }
    }

    /**
     * Update media in bucket and DB
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveUpdate($rootValue, array $args)
    {
        $inputs = $args['data'];
        if (!isset($inputs['id']))
            throw new GraphQLSaveDataException(__('media.media_id_is_not_specified'), __('Error'));

        $media = Media::where('id', $inputs['id'])->where('user_id', \Auth::user()->id)->first();
        if ($media) {
            $media->description = $inputs['description'];
            $media->save();

            return [
                'status' => 'File has been updated successfully',
                'data' => $media,
            ];
        } else {
            throw new GraphQLSaveDataException(__('media.media_with_specified_id_not_found'), __('Error'));
        }
    }

    /**
     * Delete media from bucket and DB
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveDelete($rootValue, array $args)
    {
        $inputs = $args['data'];

        if (!isset($inputs['id']))
            throw new GraphQLSaveDataException(__('media.media_id_is_not_specified'), __('Error'));

        $media = Media::where('id', $inputs['id'])->where('user_id', \Auth::user()->id)->first();
        if ($media) {
            $s3path = MediaHelper::getS3Path($media->type);
            MediaHelper::deleteMedia($s3path .'/'. \Auth::user()->id .'/'. $media->name);
            MediaHelper::deleteFolder($s3path .'/'. \Auth::user()->id .'/'. pathinfo($media->name, PATHINFO_FILENAME));
            $media->delete();
            return [
                'status' => 'File(s) have been deleted successfully',
            ];
        } else {
            throw new GraphQLSaveDataException(__('media.media_with_specified_id_not_found'), __('Error'));
        }
    }

    /**
     * View one media
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveGetOneMedia($rootValue, array $args)
    {
        $user = \Auth::user();
        $inputs = $args['data'];

        if (!isset($inputs['id']))
            throw new GraphQLSaveDataException(__('media.media_id_is_not_specified'), __('Error'));

        $media = Media::find($inputs['id']);

        if (!$media)
            throw new GraphQLSaveDataException(__('media.media_with_specified_id_not_found'), __('Error'));

        $userView = MediaUsersView::where('media_id', $inputs['id'])->where('user_id', $user->id)->first();
        if (!$userView) {
            $media->views()->create(['user_id' => $user->id]);
            $media->increment('views');
        }

        if($user->can('viewPresents', $media)){
            $presents = $media->presents()->get();
        }

        return [
            'media' => $media,
            'media_presents' => $presents ?? null
        ];
    }

    /**
     * View all users media
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveGetAllUsersMedia($rootValue, array $args)
    {
        $inputs = $args['data'];

        $user = \Auth::user();

        $medias = Media::where('user_id', $user->id)->where('type', '!=', Media::TYPE_AVATAR)->orderBy('created_at', 'desc')->paginate($inputs['perpage'], ['*'], 'page', $inputs['page']);
        $results = [];
        foreach($medias->items() as $media){
            $s3path = MediaHelper::getS3Path($media->type);
            $thumbs = [];
            foreach($inputs['thumbs'] as $prefix){
                $fileName = pathinfo($media->name, PATHINFO_FILENAME);
                if(($media->type == Media::TYPE_VIDEO) && is_numeric ($prefix)) { // && ($thumb['prefix'] == '320' || $thumb['prefix'] == '480')
                    $path = $s3path . '/' . $user->id . '/' . $fileName . '/' . $prefix . '_' . $fileName . '.jpg';
                } else {
                    $path = $s3path . '/' . $user->id . '/' . $fileName . '/' . $prefix . '_' . $media->name;
                }
                $thumbs[$prefix] = MediaHelper::getPublicUrl($path);
            }

            $results[] = [
                'id' => $media->id,
                'type' => $media->type,
                'name' => $media->name,
                'mimetype' => $media->mimetype,
                'size' => $media->size,
                'type' => $media->type,
                'views' => $media->views,
                'presents_cost' => $media->presents_cost,
                'media_uri' => $media->media_uri,
                'description' => $media->description,
                'created_at' => $media->created_at,
                'thumbs' => $thumbs
            ];
        }

        return [
            'total' => $medias->total(),
            'current_page' => $medias->currentPage(),
            'last_page' => $medias->lastPage(),
            'medias' => $results,
        ];
    }

    /**
     * Generate pre signed urls for avatar
     *
     * @param $rootValue
     * @param array $args
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveAvatarUploadGenerate($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = \Auth::user();
        $s3path = MediaHelper::getS3Path(Media::TYPE_AVATAR);
        $presignedMediaUrl = MediaHelper::createPresignedUrl(Media::TYPE_AVATAR, $inputs, $s3path . '/' . $user->id);
        $presignedThumbnailUrl = MediaHelper::createThumbnailPresignedUrl(Media::TYPE_AVATAR, $inputs, $s3path . '/' . $user->id, $presignedMediaUrl['name']);

        $results = [
            'name' => $presignedMediaUrl['name'],
            'uri' => $presignedMediaUrl['uri'],
            'mimetype' => $inputs['mimetype'],
            'size' => $inputs['size'],
            'thumbs' => $presignedThumbnailUrl,
        ];

        return [
            'status' => 'URL(s) successfully generated',
            'results' => $results,
        ];
    }

    /**
     * Store uploaded avatar into DB
     *
     * @param $rootValue
     * @param array $args
     * @return Media
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveAvatarUploadStore($rootValue, array $args)
    {
        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $user = \Auth::user();
        $s3path = MediaHelper::getS3Path(Media::TYPE_AVATAR);

        if (!MediaHelper::checkExists($s3path . '/' . $user->id . '/' . $inputs['name'])) {
            throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
        }

        $avatar = new Media();
        $avatar->type = Media::TYPE_AVATAR;

        $avatar->fill($inputs);

        if (!$avatar->save()) {
            throw new GraphQLSaveDataException(__('media.no_files_were_sent'), __('Error'));
        }

        return $avatar;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string',
            'mimetype' => 'required|string',
            'size' => 'integer|max:'. self::MEDIA_SIZE,
            'description' => 'nullable|string|max:256',
            'thumbs' => 'nullable|array|min:1'
        ];
    }
}
