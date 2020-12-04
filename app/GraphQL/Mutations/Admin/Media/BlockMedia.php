<?php

namespace App\GraphQL\Mutations\Admin\Media;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Media\BlockMediaRequest;
use App\Models\Media;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class BlockMedia
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  BlockMediaRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \ReflectionException
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, BlockMediaRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('media_ban', $user);

        $inputs = $args->validated();

        $media_id = Arr::get($inputs, 'id');

        $media = Media::whereId($media_id)->firstOrFail();

        $media->status = Media::STATUS_BANNED;
        $media->fill($inputs);

        if (!$media->save()) {
            throw new GraphQLSaveDataException(__('media.save_failed'), __('Error!'));
        }

        return $media;
    }
}
