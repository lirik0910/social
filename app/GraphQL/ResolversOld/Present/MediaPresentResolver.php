<?php


namespace App\GraphQL\ResolversOld\Present;


use App\Exceptions\GraphQLSaveDataException;
use App\Models\Media;
use App\Models\MediaPresent;
use App\Models\Present;
use App\Models\User;
use App\Events\Media\MediaPresentSent;
use Illuminate\Support\Facades\Auth;
use App\Traits\RequestDataValidate;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class MediaPresentResolver
{
    use RequestDataValidate;

    /**
     * Create media present record
     *
     * @param $rootValue
     * @param array $args
     *
     * @return array
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws GraphQLSaveDataException
     */
    public function resolveCreate($rootValue, array $args)
    {
        $user = Auth::user();

        //TODO add validation for user credit balance

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $present = Present::whereId($inputs['present_id'])->firstOrFail();
        $media = Media::whereId($inputs['media_id'])->firstOrfail();

        $media_present = new MediaPresent();
        $media_present->user_id = $user->id;
        $media_present->price = $present->price;

        $media_present->fill($inputs);

        $media->presents_cost += $present->price;

        if (!$media_present->save() || !$media->save()) {
            throw new GraphQLSaveDataException(__('media_present.create_failed'), __('Error'));
        }

        /** Send notification about new media present received **/
        event(new MediaPresentSent($media, $present, $user));

        return [
            'present' => $present,
            'media' => $media,
        ];
    }

    /**
     * Return rules array
     *
     * @return array
     */
    public function rules()
    {
        return [
            'media_id' => 'required|integer',
            'present_id' => 'required|integer',
        ];
    }
}
