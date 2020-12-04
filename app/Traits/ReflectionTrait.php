<?php

namespace App\Traits;


trait ReflectionTrait
{

    /**
     * Return assoc with available constants
     *
     * @param string $paramsPrefix
     * @param string $translatePrefix
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function availableParams(string $paramsPrefix, string $translatePrefix = '')
	{
		$constants = (new \ReflectionClass(__class__))->getConstants();

		if (empty($constants) || !$paramsPrefix) {
			return [];
		}

		$params = [];

		$paramsPrefix = strtoupper($paramsPrefix) . '_';

		foreach ($constants as $name => $value) {
			if (strpos($name, $paramsPrefix) !== 0) {
				continue;
			}

			$paramName = strtolower(substr($name, strlen($paramsPrefix)));

			$params[$value] = $translatePrefix
            ? __($translatePrefix . '_' .$paramName)
            : $paramName;
		}

		return $params;
	}
}
