<?php

namespace App\Http\Requests\User;

use App\GraphQL\Queries\User\SearchUsers;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;
use App\Models\Profile;

class SearchUsersRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
            'order_by' => 'array',
            'order_by.column' => 'integer|in:' . implode(',', array_keys(SearchUsers::availableParams('order_by'))),
            'order_by.dir' => 'in:DESC,ASC',
            'filter' => 'array',
            'filter.nickname' => 'nullable|string|max:12',
            'filter.address' => 'nullable|string',
            'filter.age' => 'required_with:filter|array',
            'filter.age.from' => 'required_with:filter.age|integer|min:' . Profile::MIN_AVAILABLE_AGE . '|max:99|lte:filter.age.to',
            'filter.age.to' => 'required_with:filter.age|integer|min:' . Profile::MIN_AVAILABLE_AGE . '|max:99|gte:filter.age.from',
            'filter.height' => 'array',
            'filter.height.from' => 'required_with:filter.height|integer|min:40|max:240|lte:filter.height.to',
            'filter.height.to' => 'required_with:filter.height|integer|min:40|max:240|gte:filter.height.from',
            'filter.sex' => 'integer|in:' . implode(',', array_keys(Profile::availableParams('gender'))),
            'filter.physique' => 'integer|in:' . implode(',', array_keys(Profile::availableParams('physique'))),
            'filter.eye' => 'integer|in:' . implode(',', array_keys(Profile::availableParams('eye_color'))),
            'filter.hair' => 'integer|in:' . implode(',', array_keys(Profile::availableParams('hair_color'))),
            'filter.meeting_cost' => 'required_if:filter.free_only,false|array',
            'filter.meeting_cost.from' => 'required_with:filter.meeting_cost|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|lte:filter.meeting_cost.to',
            'filter.meeting_cost.to' => 'required_with:filter.meeting_cost|integer|min:0|max:' . Meeting::PRICE_MAX_VALUE . '|gte:filter.meeting_cost.from',
            'filter.free_only' => 'required_with:filter|boolean',
            'filter.new_only' => 'boolean',
            'filter.charity_only' => 'boolean',
            'filter.photo_verified_only' => 'boolean',
            'filter.safe_deal_only' => 'boolean',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'order_by.column' => 1,
            'order_by.dir' => 'DESC',
            'filter' => []
        ];
    }
}
