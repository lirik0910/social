<?php

namespace App\Traits;

use App\Helpers\PhoneHelper;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;

trait RequestDataValidate
{
    use ValidatesRequests;

    /**
     * Return validated data
     *
     * @param array $data
     * @param array $rules
     *
     * @return array
     */
    protected function validatedData(array $data, array $rules = [])
    {
        $rules = $rules ? : $this->rules();
        $this->validate($data, $rules);

        return array_intersect_key($data, $rules);
    }

    /**
     * Validate request payload
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return mixed
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->getValidationFactory()->make($data, $rules, $messages, $customAttributes)->validate();
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
