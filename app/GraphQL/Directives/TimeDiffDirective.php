<?php

namespace App\GraphQL\Directives;

use Closure;
use Carbon\Carbon;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware ;
use Nuwave\Lighthouse\Exceptions\DirectiveException;

/**
 * Class TimeDiffDirective.
 * Allow to use @timeDiff in GraphQL schema.
 *
 * Usage @timeDiff([ bool abs: true ])
 * in order to display negative values set (abs: false)
 *
 * @package App\GraphQL\Directives
 */
class TimeDiffDirective extends BaseDirective implements FieldMiddleware
{
    /**
     * Directive name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'timeDiff';
    }

    /**
     * Wrap around the final field resolver.
     *
     * @param \Nuwave\Lighthouse\Schema\Values\FieldValue $fieldValue
     * @param \Closure $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        // Retrieve the existing resolver function
        /** @var Closure $previousResolver */
        $previousResolver = $fieldValue->getResolver();

        // Wrap around the resolver
        $wrappedResolver = function (...$args) use ($previousResolver) {
            // Call the resolver, passing along the resolver arguments
            /** @var string $result */
            $result = $previousResolver(...$args);

            // in case field is nullable
            if (is_null($result))
                return 0;

            if (!($result instanceof Carbon)) {
                throw new DirectiveException(__('Field should be instance of Carbon class'));
            }

            $diff =  Carbon::now()->diffInSeconds($result, false);

            if ($this->directiveArgValue('abs', true))
                return $diff > 0 ? $diff : 0;

            return $diff;
        };

        // Place the wrapped resolver back upon the FieldValue
        // It is not resolved right now - we just prepare it
        $fieldValue->setResolver($wrappedResolver);

        // Keep the middleware chain going
        return $next($fieldValue);
    }
}
