<?php

namespace App\Http\Requests\Admin\Support;

use App\Libraries\GraphQL\AbstractValidation;

class ChangeSupportModeratorRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
            'moderator_id' => 'required|string',
        ];
    }
}
