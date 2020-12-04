<?php

namespace App\GraphQL\Mutations\User;

use App\Models\User;
use App\Helpers\MediaHelper;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Exceptions\GraphQLLogicRestrictException;

class DeleteUser
{
    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Return a value for the field.
     *
     * @param null $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param mixed[] $args The arguments that were passed into the field.
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @throws GraphQLLogicRestrictException
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context)
    {
        $this->user = $context->user();
        $result = get_object_vars(\DB::select("CALL delete_user(" . $this->user->id . ")")[0]);

        if ($result['count'] < 0) {
            throw new GraphQLLogicRestrictException(__('user.active_communications_exists'), __('Error!'));
        }
        MediaHelper::deleteFolder(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_AVATAR) . '/' . $this->user->id);
        MediaHelper::deleteFolder(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_IMAGE) . '/' . $this->user->id);
        MediaHelper::deleteFolder(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION) . '/' . $this->user->id);

        return true;
    }
}
