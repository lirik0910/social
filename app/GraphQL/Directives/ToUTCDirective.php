<?php


namespace App\GraphQL\Directives;

use Carbon\Carbon;
use Nuwave\Lighthouse\Support\Contracts\ArgTransformerDirective;


class ToUTCDirective implements ArgTransformerDirective
{
    /**
     * Name of the directive as used in the schema.
     *
     * @return string
     */
    public function name(): string
    {
        return 'toUTC';
    }

    /**
     * Convert input date to UTC timezone.
     *
     * @param  string  $argumentValue
     * @return mixed
     */
    public function transform($result): string
    {
        return $result instanceof Carbon ? $result->setTimezone('UTC') : $result;
    }
}
