<?php


namespace App\GraphQL\Mutations\User;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\User;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class TogglePrivacy
{
    use RequestDataValidate;

    protected $user;

    /**
     * Change User Password
     *
     * @param       $rootValue
     * @param array $args
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     *
     * @return string
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     * @throws GraphQLLogicRestrictException
     */
    public function resolve($rootValue, $args, GraphQLContext $context)
    {
        $this->user = $context->user();

        if(!$this->user->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
            if($this->checkCurrentEventsExists()) {
                throw new GraphQLLogicRestrictException(__('user.current_events_exists'), __('Error!'));
            }
        }

        $this->user->toggleFlag(User::FLAG_PRIVATE_PROFILE);

        if (!$this->user->save()) {
            throw new GraphQLSaveDataException(__('Save data failed'), __('Error'));
        }

        return $this->user;
    }

    protected function checkCurrentEventsExists()
    {
        return DB
            ::query()
            ->select(DB::raw(1))
            ->orWhereExists(function ($query) {
                $query
                    ->select(DB::raw(1))
                    ->from('auctions')
                    ->where('user_id', $this->user->id)
                    ->where(function ($q) {
                        $q->where('end_at', '>', Carbon::now());
                        $q->whereNull('cancelled_at');
                    });
            })
            ->orWhereExists(function ($query) {
                $query
                    ->select(DB::raw(1))
                    ->from('adverts')
                    ->where('user_id', $this->user->id)
                    ->where(function ($q) {
                        $q->where('end_at', '>', Carbon::now());
                        $q->whereNull('cancelled_at');
                        $q->whereNull('respond_id');
                    });
            })
            ->orWhereExists(function ($query) {
                $query
                    ->select(DB::raw(1))
                    ->from('users_private_chat_rooms')
                    ->where('seller_id', $this->user->id)
                    ->whereNull('ended_at');
            })
            ->exists();
    }
}
