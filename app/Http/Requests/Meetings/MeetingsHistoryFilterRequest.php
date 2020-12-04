<?php

namespace App\Http\Requests\Meetings;

use App\GraphQL\Queries\Meeting\MeetingsHistory;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;

class MeetingsHistoryFilterRequest extends AbstractValidation
{
    protected $dataLocation = 'filter';

    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'type' => 'required|integer|in:' . implode(',', array_keys(MeetingsHistory::availableParams('type'))),
            'status' => 'nullable|integer|in:' . implode(',', array_keys(Meeting::availableParams('status'))),
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'status' => null,
        ];
    }
}
