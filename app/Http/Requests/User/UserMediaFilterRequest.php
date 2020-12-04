<?php


namespace App\Http\Requests\User;

use App\GraphQL\Queries\MeetingsHistory;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;

class UserMediaFilterRequest extends AbstractValidation
{
    protected $dataLocation = 'filter';

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function defaultValues(): array
    {
        return [
            'limit' => 24,
            'offset' => 0,
        ];
    }
}
