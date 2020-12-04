<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class ToggleTwoStepVerificationRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'two_step_verification' => 'required|boolean'
        ];
    }
}
