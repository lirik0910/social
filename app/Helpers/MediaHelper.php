<?php

namespace App\Helpers;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\Media;
use App\Models\ProfilesBackground;
use App\Models\User;
use App\Traits\ReflectionTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Storage;
use Illuminate\Support\Str;

class MediaHelper
{
    use ReflectionTrait;

    const FILE_TYPE_MEDIA_IMAGE = 1;
    const FILE_TYPE_MEDIA_VIDEO = 2;
    const FILE_TYPE_MEDIA_AVATAR = 3;
    const FILE_TYPE_PROFILE_BACKGROUND = 4;
    const FILE_TYPE_ADVERT_PREVIEW = 5;
    const FILE_TYPE_PHOTO_VERIFICATION = 6;
    const FILE_TYPE_PHOTO_VERIFICATION_SIGN = 7;
    const FILE_TYPE_PRESENT_IMAGE = 8;
    const FILE_TYPE_PRESENT_CATEGORY_IMAGE = 9;
    const FILE_TYPE_CHARITY_ORGANIZATION_IMAGE = 10;

    const BUCKET_ROOT_PATH_MEDIA = 'media/users';
    const BUCKET_ROOT_PATH_AVATAR = 'avatar/users';
    const BUCKET_ROOT_PATH_PROFILE_BACKGROUND = 'profiles_backgrounds';
    const BUCKET_ROOT_PATH_ADVERT = 'adverts/users';
    const BUCKET_ROOT_PATH_PHOTO_VERIFICATION = 'verification_photos/users';
    const BUCKET_ROOT_PATH_PHOTO_VERIFICATION_SIGN = 'verification_signs';
    const BUCKET_ROOT_PATH_PRESENT_IMAGE = 'presents';
    const BUCKET_ROOT_PATH_CHARITY_ORGANIZATION_IMAGE = 'charity_organizations';


    /**
     * Get filesystem storage
     *
     * @return mixed
     */
    public static function getStorage()
    {
        return Storage::disk('s3');
    }

    /**
     * Get client
     *
     * @return mixed
     */
    public static function getClient()
    {
        return self::getStorage()->getDriver()->getAdapter()->getClient();
    }

    /**
     * Get bucket
     *
     * @return mixed
     */
    public static function getBucket()
    {
        return self::getStorage()->getDriver()->getAdapter()->getBucket();
    }

    public static function putObject($path, $name, $path_to_file, $mimetype = null)
    {
        $data = [
            'Bucket' => self::getBucket(),
            'Key' => $path . '/' . $name,
            'SourceFile' => $path_to_file,
            'ACL' => 'public-read'
        ];

        if (!empty($mimetype)) {
            $data['ContentType'] = $mimetype;
        }

        return self::getClient()->putObject($data);
    }

    /**
     * Generate new media name
     *
     * @param integer $type
     * @param string $file_name
     *
     * @return string
     */
    public static function generateMediaName($type, $file_name)
    {
        $ext = $type === Media::TYPE_VIDEO ? 'mp4' : pathinfo($file_name, PATHINFO_EXTENSION);

        return Str::random(30) . '.' . $ext;
    }

    /**
     * Create pre signed url
     *
     * @param integer $type
     * @param array $input
     * @param string $path
     *
     * @return array
     */
    public static function createPresignedUrl($type, $input, $path)
    {
        $customName = self::generateMediaName($type, $input['name']);
        $request = self::generatePresignedUrl($path, $customName, $input['mimetype']);

        return [
            'name' => $customName,
            'uri' => (string)$request->getUri(),
        ];
    }

    /**
     * Create thumbnail pre signed url
     *
     * @param integer $type
     * @param array $input
     * @param string $path
     * @param string $mediaName
     *
     * @return array
     */
    public static function createThumbnailPresignedUrl($type, $input, $path, $mediaName)
    {
        $thumbnailFolderName = pathinfo($mediaName, PATHINFO_FILENAME);
        $thumbnailExtension = pathinfo($mediaName, PATHINFO_EXTENSION);
        if ($thumbnailExtension == 'jpeg') {
            $thumbnailExtension = 'jpg';
        }

        $results = [];

        if (array_key_exists('thumbs', $input)) {
            foreach ($input['thumbs'] as $thumb) {
                $ext = MediaHelper::checkExtension($mediaName);
                if (is_numeric($thumb['prefix'])) {
                    $request = self::generatePresignedUrl($path . '/' . $thumbnailFolderName, $thumb['prefix'] . '_' . pathinfo($mediaName, PATHINFO_FILENAME) . '.' . $ext, $thumb['mimetype']);
                    $results[$thumb['prefix']] = [
                        'prefix' => $thumb['prefix'],
                        'mimetype' => $thumb['mimetype'],
                        'rname' => $thumb['name'],
                        'name' => $thumb['prefix'] . '_' . pathinfo($mediaName, PATHINFO_FILENAME) . '.' . $ext,
                        'uri' => (string)$request->getUri(),
                    ];
                } else {
                    $request = self::generatePresignedUrl($path . '/' . $thumbnailFolderName, $thumb['prefix'] . '_' . $mediaName, $thumb['mimetype']);
                    $results[$thumb['prefix']] = [
                        'prefix' => $thumb['prefix'],
                        'mimetype' => $thumb['mimetype'],
                        'rname' => $thumb['name'],
                        'name' => $thumb['prefix'] . '_' . $mediaName,
                        'uri' => (string)$request->getUri(),
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Generate pre signed url
     *
     * @param string $path
     * @param string $name
     * @param string $mimetype
     *
     * @return array
     */
    public static function generatePresignedUrl($path, $name, $mimetype)
    {
        $command = self::getClient()->getCommand('PutObject', [
            'Bucket' => self::getBucket(),
            'Key' => $path . '/' . $name,
            'Content-Type' => $mimetype,
            'ACL' => 'public-read'
        ]);
        $request = self::getClient()->createPresignedRequest($command, '+5 minutes');

        return $request;
    }

    /**
     * Check media exists
     *
     * @param string $path
     *
     *
     * @return bool
     */
    public static function checkExists($path)
    {
        return self::getClient()->doesObjectExist(self::getBucket(), $path);
    }

    /**
     * Get public url
     *
     * @param string $name
     *
     * @return string
     */
    public static function getPublicUrl($name)
    {
        return (string)self::getStorage()->url($name);
    }

    /**
     * Delete media from storage
     *
     * @param string $name
     *
     * @return bool
     */
    public static function deleteMedia($name)
    {
        self::getStorage()->delete($name);

        return true;
    }

    /**
     * Delete media folder from storage
     *
     * @param string $name
     *
     * @return bool
     */
    public static function deleteFolder($name)
    {
        self::getStorage()->deleteDirectory($name);

        return true;
    }


    /**
     * Get s3 root path
     *
     * @param integer $type
     * @return string
     * @throws GraphQLLogicRestrictException
     *
     */
    public static function getS3Path($type)
    {
        switch ($type) {
            case self::FILE_TYPE_MEDIA_IMAGE:
            case self::FILE_TYPE_MEDIA_VIDEO:
                $s3path = self::BUCKET_ROOT_PATH_MEDIA;
                break;
            case self::FILE_TYPE_MEDIA_AVATAR:
                $s3path = self::BUCKET_ROOT_PATH_AVATAR;
                break;
            case self::FILE_TYPE_PROFILE_BACKGROUND:
                $s3path = self::BUCKET_ROOT_PATH_PROFILE_BACKGROUND;
                break;
            case self::FILE_TYPE_ADVERT_PREVIEW:
                $s3path = self::BUCKET_ROOT_PATH_ADVERT;
                break;
            case self::FILE_TYPE_PHOTO_VERIFICATION:
                $s3path = self::BUCKET_ROOT_PATH_PHOTO_VERIFICATION;
                break;
            case self::FILE_TYPE_PHOTO_VERIFICATION_SIGN:
                $s3path = self::BUCKET_ROOT_PATH_PHOTO_VERIFICATION_SIGN;
                break;
            case self::FILE_TYPE_CHARITY_ORGANIZATION_IMAGE:
                $s3path = self::BUCKET_ROOT_PATH_CHARITY_ORGANIZATION_IMAGE;
                break;
            case self::FILE_TYPE_PRESENT_IMAGE:
            case self::FILE_TYPE_PRESENT_CATEGORY_IMAGE:
                $s3path = self::BUCKET_ROOT_PATH_PRESENT_IMAGE;
                break;
            default:
                throw new GraphQLLogicRestrictException(__('common.file_type_missing'), __('Error'));
        }

        return $s3path;
    }

    /**
     * Get available mimetypes for upload
     *
     * @return array
     */
    public static function getAvailableMimetypes()
    {
        return [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/svg',
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime',
            'video/3gpp',
            'video/3gpp2',
            'video/x-m4v',
            'video/x-matroska',
        ];
    }

    /**
     * Get sizes for thumbnails
     *
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getThumbSizes($object = null)
    {
        $thumb_sizes = [
            'media' => [
                1 => '320',
                2 => '640'
            ],
            'stream' => [
                1 => '480'
            ],
            'avatar' => [
                1 => '320',
                2 => '640'
            ],
            'background' => [
                1 => '320'
            ]
        ];

        if (!empty($object)) {
            $key = self::getThumbnailClassKey($object);

            $thumb_sizes = Arr::get($thumb_sizes, $key);
        }

        return $thumb_sizes;
    }

    /**
     * @param $object
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    protected static function getThumbnailClassKey($object)
    {
        switch (get_class($object)) {
            case User::class:
                $key = 'avatar';
                break;
            case Media::class:
                if ($object->type === Media::TYPE_AVATAR) {
                    $key = 'avatar';
                } else {
                    $key = 'media';
                }
                break;
            case ProfilesBackground::class:
                $key = 'background';
                break;
            default:
                throw new GraphQLLogicRestrictException(__('media.wrong_class_for_thumbnail'), __('Error!'));
        }

        return $key;
    }

    /**
     * @param $path
     * @return string
     */
    public static function checkExtension($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'gif':
                $ext = 'gif';
                break;
            case 'jpeg':
                $ext = 'jpg';
                break;
            default:
                $ext = 'jpg';
        }

        return $ext;
    }

    /**
     * Generate random name and upload image to AWS (for admin panel files)
     *
     * @param UploadedFile $file
     * @param $s3path
     * @return array
     */
    public static function uploadAdminImage(UploadedFile $file, $s3path)
    {
        $ext = $file->getClientOriginalExtension();

        $name = Str::random(30) . '.' . $ext;

        if ($ext === 'svg') {
            MediaHelper::putObject($s3path, $name, $file->path(), 'image/svg+xml');
        } else {
            MediaHelper::putObject($s3path, $name, $file->path());
        }

        return [
            'name' => $name,
            'mimetype' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    /**
     * @param UploadedFile $file
     * @param $s3path
     * @param $parent
     * @param string $file_type
     * @throws GraphQLLogicRestrictException
     * @throws \ImagickException
     */
    public static function makeAndUploadAdminThumbnail(UploadedFile $file, $s3path, $parent, string $file_type)
    {
        //TODO Change $file_type string argument on MediaHelper FILE_TYPE constants logic
        $thumbnail_sizes = self::getThumbSizes()[$file_type];

        foreach ($thumbnail_sizes as $size) {
            $parent_name = is_object($parent)
                ? $parent->name
                : $parent['name'];

            $tmp_file_path = $file->path();
            $thumbnail_name = $size . '_' . pathinfo($parent_name, PATHINFO_BASENAME);
            $thumbnail_folder = pathinfo($parent_name, PATHINFO_FILENAME);

            $img = new \Imagick($tmp_file_path);

            if ($img->getImageHeight() > $img->getImageWidth()) {
                $img->thumbnailImage((int) $size, 0);
            } else {
                $img->thumbnailImage(0, (int) $size);
            }

            $thumb_path = Storage::disk('public')->path($thumbnail_name);

            $img->writeImage($thumb_path);

            MediaHelper::putObject($s3path . '/' . $thumbnail_folder, $thumbnail_name, $thumb_path);

            Storage::disk('public')->delete($thumbnail_name);
        }
    }
}
