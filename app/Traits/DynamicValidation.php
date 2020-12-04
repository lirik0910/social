<?php

namespace App\Traits;

trait DynamicValidation
{

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call(string $method, array $arguments)
    {
        $methodData = new \ReflectionMethod(self::class, $method);
        $params = $methodData->getParameters();
        $data = is_array($params) && array_key_exists(1, $params) ? $params[1] : null;

        if ($data && !$data->isArray()) {
            $className = $data->getClass()->name;
            $arguments[1] = (new $className($arguments[1]));

        }

        return $this->{$method}(...$arguments);
    }
}
