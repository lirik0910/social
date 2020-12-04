<?php


namespace App\GraphQL;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Closure;
use GraphQL\Error\Error;
use Illuminate\Auth\AuthenticationException;

class ErrorHandler implements \Nuwave\Lighthouse\Execution\ErrorHandler
{
    public static function handle(Error $error, Closure $next): array
    {
        $error_category = $error->getCategory();

        if (in_array($error_category, ['logic', 'validation', 'code'])) {
            Bugsnag::notifyException($error, function ($report) use ($error_category) {
                $report->setMetaData([
                    'error_info' => array(
                        'category' => $error_category,
                    )
                ]);
            });
        }

        if ($error->getPrevious() instanceof AuthenticationException) {
            throw $error->getPrevious();
        }

        return $next($error);
    }
}
