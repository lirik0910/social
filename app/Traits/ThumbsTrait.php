<?php


namespace App\Traits;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Models\Media;
use App\Models\ProfilesBackground;
use App\Models\User;
use App\Helpers\MediaHelper;

trait ThumbsTrait
{

    /**
     * Get thumbs for requested sizes
     *
     * @param $self
     * @param $args
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public function getThumbs($self, $args)
    {
        $thumbs = [];

        $sizes = empty($args['sizes']) ? MediaHelper::getThumbSizes($self) : $args['sizes'];

        if(is_array($sizes) && count($sizes)) {
            foreach ($sizes as $size) {
                $uri = $this->getUri($self, $size);

                $thumbs[$size] = $uri;
            }
        }

        return $thumbs;
    }

    /**
     * Get uri for specific file and size
     *
     * @param        $instance
     * @param string $size
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function getUri($instance, string $size)
    {
        $uri = null;

        switch(get_class($instance)) {
            case Media::class:
                $ext = MediaHelper::checkExtension($instance->name);
                if($instance->type === Media::TYPE_AVATAR) {
                    $dir_path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_AVATAR) . '/' . $instance->user_id .'/' . pathinfo($instance->name, PATHINFO_FILENAME);

                } else {
                    $s3path = ($instance->type === Media::TYPE_VIDEO) ? MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_VIDEO) : MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_IMAGE);
                    $dir_path = $s3path . '/' . $instance->user_id . '/' . pathinfo($instance->name, PATHINFO_FILENAME);
                }

                if($size === 'preview') {
                    $uri = MediaHelper::getPublicUrl($dir_path . '/' . $size . '_' . $instance->name);
                } else {
                    $uri = MediaHelper::getPublicUrl($dir_path . '/' . $size . '_' . pathinfo($instance->name, PATHINFO_FILENAME)) . '.' . $ext;
                }
                break;
            case ProfilesBackground::class:
                $ext = MediaHelper::checkExtension($instance->name);
                $dir_path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND);

                if($instance->user_id) {
                    $dir_path .= '/users';
                }

                $uri = MediaHelper::getPublicUrl($dir_path . '/' . pathinfo($instance->name, PATHINFO_FILENAME) . '/' . $size . '_' . pathinfo($instance->name, PATHINFO_FILENAME) . '.' . $ext);
                break;

            case User::class:
                $ext = MediaHelper::checkExtension($instance->image);
                if(!empty($instance->image) && !$instance->isAvatarHidden()) {
                    $dir_path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_AVATAR)  . '/' . $instance->id .'/' . pathinfo($instance->image, PATHINFO_FILENAME);

                    if($size === 'preview') {
                        $uri = MediaHelper::getPublicUrl($dir_path . '/' . $size . '_' . $instance->image);
                    } else {
                        $uri = MediaHelper::getPublicUrl($dir_path . '/' . $size . '_' . pathinfo($instance->image, PATHINFO_FILENAME)) . '.' . $ext;
                    }
                }

                break;

            default:
                break;
        }

        return $uri;
    }

}
