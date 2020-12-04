<?php


namespace App\GraphQL\Directives;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Access\Gate;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Exceptions\DirectiveException;
use Nuwave\Lighthouse\Schema\Directives\CanDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\DefinedDirective;

/**
 * Class PolicyDirective.
 * Allow to use @policy in GraphQL schema.
 *
 * @package App\GraphQL\Directives
 */
class PolicyDirective extends BaseDirective implements FieldMiddleware, DefinedDirective
{
    /**
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * PolicyDirective constructor.
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'policy';
    }

    /**
     * Ensure the user is authorized to access this field.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $fieldValue
     * @param  \Closure  $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        $previousResolver = $fieldValue->getResolver();

        return $next(
            $fieldValue->setResolver(
                function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($previousResolver) {
                    // Directive arguments
                    $dir_args = $this->getArguments();
                    // Input arguments
                    $inputs = $args['data'] ?? $args;

                    $find_param = $inputs[$dir_args['find_param']] ?? null;

                    // Find and add model to gate arguments array
                    $gate_arguments[0] = $find_param ? $dir_args['model']::findOrFail($find_param) : $dir_args['model'];
                    // Add additional args from input to gate arguments array
                    $gate_arguments[1] = array_intersect_key($inputs, array_flip($dir_args['args']));

                    $this->authorize($context->user, $gate_arguments);

                    return call_user_func_array($previousResolver, func_get_args());
                }
            )
        );
    }

    /**
     * Get default model class and arguments name that are passed to `Gate::check`.
     *
     * @return array
     * @throws DefinitionException
     */
    public function getArguments(): array
    {
        $modelClass = $this->getModelClass();
        $find = $this->directiveArgValue('find', 'id');
        $args = (array) $this->directiveArgValue('args');

        return [
            'model' => $modelClass,
            'find_param' => $find,
            'args' => $args
        ];
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @param array $args
     * @return void
     *
     * @throws \Nuwave\Lighthouse\Exceptions\AuthorizationException
     */
    protected function authorize($user, array $args): void
    {
        $can = $this->gate
            ->forUser($user)
            ->check(
                $this->directiveArgValue('ability'),
                $args
            );

        if (! $can) {
            throw new AuthorizationException(
                "You are not authorized to access {$this->definitionNode->name->value}"
            );
        }
    }

    public static function definition(): string
    {
        return /* @lang GraphQL */ <<<'SDL'
"""
Check a Laravel Policy to ensure the current user is authorized to access a field.
"""
directive @policy(
  """
  The ability to check permissions for.
  """
  ability: String!

  """
  The name of the argument that is used to find a specific model
  instance against which the permissions should be checked.
  """
  find: String

  """
  Additional arguments that are passed to `Gate::check`.
  """
  args: [String!]
) on FIELD_DEFINITION
SDL;
    }
}
