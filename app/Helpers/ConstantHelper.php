<?php

namespace App\Helpers;

class ConstantHelper
{
    /**
     * @param $prefix
     * @param $class
     * @return array
     * @throws \ReflectionException
     */
    public static function getConstants($prefix, $class)
    {
        $constants = (new \ReflectionClass($class))->getConstants();

        $constants = array_filter($constants, function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);


        if (empty($constants))
            throw new \RuntimeException('Don\'t exist constants with the prefix ' . $prefix);

        return $constants;
    }

    public static function getConstantValueByKey($prefix, $class, $key)
    {
        $key = strtoupper($prefix . '_' . $key);
        $constants = self::getConstants($prefix, $class);
        if (!array_key_exists($key, $constants)) {
            throw new \RuntimeException('Don\'t exist key in constants with the prefix ' . $prefix);
        }

        return $constants[$key];
    }

    public static function getConstantKeyByValue($prefix, $class, $value)
    {
        $constants = self::getConstants($prefix, $class);

        return array_search($value, $constants);
    }

}
