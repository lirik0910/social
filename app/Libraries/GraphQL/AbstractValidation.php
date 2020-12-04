<?php


namespace App\Libraries\GraphQL;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

abstract class AbstractValidation
{
    use ValidatesRequests;

    protected $dataLocation = 'data';
    protected $data = [];

    public function __construct($data)
    {
        $this->setData($data);
        $this->validate();
    }

    /**
     * Validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     */
    public function validated() : array
    {
        $defaultValues = $this->defaultValues();
        $results = [];

        $missingValue = Str::random(10);

        foreach (array_keys($this->rules()) as $key) {
            $value = data_get($this->data, $key, $missingValue);

            if ($value !== $missingValue) {
                if(!Str::contains($key, ['*'])) {
                    Arr::set($results, $key, $value);
                }
            } elseif (array_key_exists($key, $defaultValues)) {
                Arr::set($results, $key, $defaultValues[$key]);
            }
        }

        return $results;
    }

    /**
     * Validate request payload
     *
     * @return void
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    protected function validate() : void
    {
        try {
            $this->getValidationFactory()->make($this->data, $this->rules(), $this->messages(), $this->customAttributes())->validate();
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

    }

    /**
     * Set data that should be validated
     *
     * @param array $data
     */
    protected function setData(array $data) : void
    {
        if ($this->dataLocation && array_key_exists($this->dataLocation, $data))
            $data = $data[$this->dataLocation];

        if (is_array($data) && !empty($data))
            $this->data = $data;
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    protected function messages() : array
    {
        return [];
    }

    /**
     * Custom attributes
     *
     * @return array
     */
    protected function customAttributes() : array
    {
        return [];
    }

    /**
     * Default values that will be returned by validated function,
     * if particular data missing in request
     *
     * @return array
     */
    protected function defaultValues() : array
    {
        return [];
    }
}
