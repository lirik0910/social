<?php

namespace App\Http\Requests\Auth;

use App\Helpers\PhoneHelper;
use App\Libraries\GraphQL\AbstractValidation;
use App\Rules\Recaptcha;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class RegistrationRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $rules = [
            'recaptcha' => ['required', new Recaptcha()],
            'code' => 'required|string|in:' . implode(',', PhoneHelper::phoneCodes()),
            'number' => 'required|regex:/^\d{4,13}$/',
            'password' => 'required|string|min:8|max:22|confirmed|regex:/^[a-zA-Z\d@\!\?#\+\-$%^{}\[\]\(\)\~\,\;\:\.\<\>\'\\\"\/\&\*\`]{8,22}$/',
            'adult' => 'required|boolean|accepted',
        ];

        $mobile_client_header = Request::header('App-Client');
        $mobile_client_header_exists = !empty($mobile_client_header) && in_array($mobile_client_header, ['android', 'ios']);

        if (env('APP_ENV') != "production" || $mobile_client_header_exists) {
            Arr::forget($rules, 'recaptcha');
        }

        return $rules;
    }
}
