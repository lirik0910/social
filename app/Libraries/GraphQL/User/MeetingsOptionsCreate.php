<?php


namespace App\Libraries\GraphQL\User;


use App\Exceptions\GraphQLSaveDataException;
use Illuminate\Support\Facades\Auth;

class MeetingsOptionsCreate
{
    /**
     * @throws GraphQLSaveDataException
     */
    public function resolve()
    {
        $user = Auth::user();

        if (!$user->meetings_options()->create()) {
           throw new GraphQLSaveDataException(__('user.failed_to_save_meetings_options'), _('Error!'));
        }

        return;
    }
}
